<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { useScaling } from '../../../shared/composables/useScaling.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

// State
const isLoading = ref(false)
const searchTerm = ref('')
const selectedCategory = ref('all')
const showAddForm = ref(false)
const editingItem = ref(null)

// Form data
const formData = ref({
  sku: '',
  name: '',
  category: '',
  description: '',
  currentStock: 0,
  minStock: 0,
  maxStock: 0,
  unitCost: 0,
  unitPrice: 0,
  location: ''
})

// Computed
const filteredItems = computed(() => {
  let items = store.stockItems
  
  if (searchTerm.value) {
    items = items.filter(item => 
      item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      item.sku.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (selectedCategory.value !== 'all') {
    items = items.filter(item => item.category === selectedCategory.value)
  }
  
  return items
})

const categories = computed(() => {
  const cats = new Set(store.stockItems.map(item => item.category))
  return Array.from(cats).map(cat => ({ value: cat, label: cat }))
})

// Actions
const handleAddItem = () => {
  showAddForm.value = true
  editingItem.value = null
  resetForm()
}

const handleEditItem = (item) => {
  editingItem.value = item
  formData.value = { ...item }
  showAddForm.value = true
}

const handleSaveItem = async () => {
  isLoading.value = true
  
  try {
    if (editingItem.value) {
      // Update existing item
      const index = store.stockItems.findIndex(item => item.id === editingItem.value.id)
      if (index !== -1) {
        store.stockItems[index] = { ...formData.value, lastUpdated: new Date().toISOString() }
      }
    } else {
      // Add new item
      await store.addStockItem(formData.value)
    }
    
    showAddForm.value = false
    resetForm()
  } catch (error) {
    console.error('Error saving item:', error)
  } finally {
    isLoading.value = false
  }
}

const handleDeleteItem = async (itemId) => {
  if (confirm('Are you sure you want to delete this item?')) {
    const index = store.stockItems.findIndex(item => item.id === itemId)
    if (index !== -1) {
      store.stockItems.splice(index, 1)
    }
  }
}

const handleAdjustStock = async (itemId, newQuantity, reason) => {
  await store.updateStockLevel(itemId, newQuantity, reason, 'Manual Adjustment')
}

const resetForm = () => {
  formData.value = {
    sku: '',
    name: '',
    category: '',
    description: '',
    currentStock: 0,
    minStock: 0,
    maxStock: 0,
    unitCost: 0,
    unitPrice: 0,
    location: ''
  }
}

onMounted(() => {
  // Load data if needed
})
</script>

<template>
  <div class="stock-management">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="{ fontSize: `${fontSizes.large}px` }" class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        Stock Management
      </h1>
      <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
        Manage your inventory items and stock levels
      </p>
    </div>

    <!-- Controls -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <InventoryInput
          v-model="searchTerm"
          placeholder="Search items by name or SKU..."
          icon="fas fa-search"
        />
      </div>
      <div class="w-full sm:w-48">
        <InventoryDropdown
          v-model="selectedCategory"
          :options="[{ value: 'all', label: 'All Categories' }, ...categories]"
          placeholder="Filter by category"
        />
      </div>
      <InventoryButton
        @click="handleAddItem"
        variant="primary"
        size="md"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Item
      </InventoryButton>
    </div>

    <!-- Items Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                SKU
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Category
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Current Stock
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Unit Cost
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="item in filteredItems" :key="item.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ item.sku }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ item.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ item.category }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <div class="flex items-center">
                  <span class="font-medium">{{ item.currentStock }}</span>
                  <span v-if="item.currentStock <= item.minStock" class="ml-2 text-red-500">
                    <i class="fas fa-exclamation-triangle"></i>
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${{ item.unitCost.toFixed(2) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge
                  :status="item.status"
                  :text="item.status === 'active' ? 'Active' : 'Inactive'"
                />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <InventoryButton
                    @click="handleEditItem(item)"
                    variant="ghost"
                    size="sm"
                  >
                    <i class="fas fa-edit"></i>
                  </InventoryButton>
                  <InventoryButton
                    @click="handleDeleteItem(item.id)"
                    variant="ghost"
                    size="sm"
                    class="text-red-600 hover:text-red-800"
                  >
                    <i class="fas fa-trash"></i>
                  </InventoryButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          {{ editingItem ? 'Edit Item' : 'Add New Item' }}
        </h2>
        
        <form @submit.prevent="handleSaveItem" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="formData.sku"
              label="SKU"
              placeholder="Enter SKU"
              required
            />
            <InventoryInput
              v-model="formData.name"
              label="Item Name"
              placeholder="Enter item name"
              required
            />
            <InventoryInput
              v-model="formData.category"
              label="Category"
              placeholder="Enter category"
              required
            />
            <InventoryInput
              v-model="formData.location"
              label="Location"
              placeholder="Warehouse location"
            />
            <InventoryInput
              v-model.number="formData.currentStock"
              label="Current Stock"
              type="number"
              min="0"
            />
            <InventoryInput
              v-model.number="formData.minStock"
              label="Minimum Stock"
              type="number"
              min="0"
            />
            <InventoryInput
              v-model.number="formData.maxStock"
              label="Maximum Stock"
              type="number"
              min="0"
            />
            <InventoryInput
              v-model.number="formData.unitCost"
              label="Unit Cost"
              type="number"
              step="0.01"
              min="0"
            />
            <InventoryInput
              v-model.number="formData.unitPrice"
              label="Unit Price"
              type="number"
              step="0.01"
              min="0"
            />
          </div>
          
          <InventoryInput
            v-model="formData.description"
            label="Description"
            type="textarea"
            placeholder="Enter item description"
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
              {{ editingItem ? 'Update' : 'Add' }} Item
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stock-management {
  padding: 0;
}
</style>
