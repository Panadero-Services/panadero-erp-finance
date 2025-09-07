<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { useScaling } from '../../../shared/composables/useScaling.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import StatusBadge from './ui/StatusBadge.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

// State
const isLoading = ref(false)
const showAddForm = ref(false)
const editingWarehouse = ref(null)

// Form data
const formData = ref({
  name: '',
  location: '',
  capacity: 0,
  manager: '',
  zones: []
})

// Actions
const handleAddWarehouse = () => {
  showAddForm.value = true
  editingWarehouse.value = null
  resetForm()
}

const handleEditWarehouse = (warehouse) => {
  editingWarehouse.value = warehouse
  formData.value = { ...warehouse }
  showAddForm.value = true
}

const handleSaveWarehouse = async () => {
  isLoading.value = true
  
  try {
    if (editingWarehouse.value) {
      // Update existing warehouse
      const index = store.warehouses.findIndex(w => w.id === editingWarehouse.value.id)
      if (index !== -1) {
        store.warehouses[index] = { ...formData.value }
      }
    } else {
      // Add new warehouse
      await store.addWarehouse(formData.value)
    }
    
    showAddForm.value = false
    resetForm()
  } catch (error) {
    console.error('Error saving warehouse:', error)
  } finally {
    isLoading.value = false
  }
}

const handleDeleteWarehouse = async (warehouseId) => {
  if (confirm('Are you sure you want to delete this warehouse?')) {
    const index = store.warehouses.findIndex(w => w.id === warehouseId)
    if (index !== -1) {
      store.warehouses.splice(index, 1)
    }
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    location: '',
    capacity: 0,
    manager: '',
    zones: []
  }
}

const getCapacityPercentage = (warehouse) => {
  return Math.round((warehouse.currentCapacity / warehouse.capacity) * 100)
}
</script>

<template>
  <div class="warehouse-management">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="{ fontSize: `${fontSizes.large}px` }" class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        Warehouse Management
      </h1>
      <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
        Manage your warehouse locations and capacity
      </p>
    </div>

    <!-- Controls -->
    <div class="mb-6">
      <InventoryButton
        @click="handleAddWarehouse"
        variant="primary"
        size="md"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Warehouse
      </InventoryButton>
    </div>

    <!-- Warehouses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="warehouse in store.warehouses"
        :key="warehouse.id"
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ warehouse.name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ warehouse.location }}</p>
          </div>
          <StatusBadge
            :status="warehouse.status"
            :text="warehouse.status === 'active' ? 'Active' : 'Inactive'"
          />
        </div>

        <div class="space-y-3">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-600 dark:text-gray-400">Capacity Usage</span>
              <span class="text-gray-900 dark:text-white">{{ getCapacityPercentage(warehouse) }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${getCapacityPercentage(warehouse)}%` }"
              ></div>
            </div>
          </div>

          <div class="text-sm text-gray-600 dark:text-gray-400">
            <p><strong>Manager:</strong> {{ warehouse.manager }}</p>
            <p><strong>Capacity:</strong> {{ warehouse.currentCapacity.toLocaleString() }} / {{ warehouse.capacity.toLocaleString() }}</p>
            <p><strong>Zones:</strong> {{ warehouse.zones.join(', ') }}</p>
          </div>
        </div>

        <div class="flex justify-end space-x-2 mt-4">
          <InventoryButton
            @click="handleEditWarehouse(warehouse)"
            variant="ghost"
            size="sm"
          >
            <i class="fas fa-edit"></i>
          </InventoryButton>
          <InventoryButton
            @click="handleDeleteWarehouse(warehouse.id)"
            variant="ghost"
            size="sm"
            class="text-red-600 hover:text-red-800"
          >
            <i class="fas fa-trash"></i>
          </InventoryButton>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          {{ editingWarehouse ? 'Edit Warehouse' : 'Add New Warehouse' }}
        </h2>
        
        <form @submit.prevent="handleSaveWarehouse" class="space-y-4">
          <InventoryInput
            v-model="formData.name"
            label="Warehouse Name"
            placeholder="Enter warehouse name"
            required
          />
          
          <InventoryInput
            v-model="formData.location"
            label="Location"
            placeholder="Enter warehouse location"
            required
          />
          
          <InventoryInput
            v-model.number="formData.capacity"
            label="Total Capacity"
            type="number"
            min="0"
            required
          />
          
          <InventoryInput
            v-model="formData.manager"
            label="Manager"
            placeholder="Enter manager name"
            required
          />
          
          <div class="flex justify-end space-x-3 pt-4">
            <InventoryButton
              @click="showAddForm = false"
              variant="ghost"
              type="button"
            >
              Cancel
            </InventoryButton>
            <InventoryButton
              type="submit"
              variant="primary"
              :loading="isLoading"
            >
              {{ editingWarehouse ? 'Update' : 'Add' }} Warehouse
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.warehouse-management {
  padding: 0;
}
</style>
