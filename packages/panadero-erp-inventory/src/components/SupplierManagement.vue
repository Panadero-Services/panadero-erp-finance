<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { useScaling } from 'panadero-shared-styling'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import StatusBadge from './ui/StatusBadge.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

// State
const isLoading = ref(false)
const showAddForm = ref(false)
const editingSupplier = ref(null)
const searchTerm = ref('')

// Form data
const formData = ref({
  name: '',
  contactPerson: '',
  email: '',
  phone: '',
  address: '',
  paymentTerms: '',
  rating: 0
})

// Computed
const filteredSuppliers = computed(() => {
  if (!searchTerm.value) return store.suppliers
  
  return store.suppliers.filter(supplier => 
    supplier.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    supplier.contactPerson.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    supplier.email.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

// Actions
const handleAddSupplier = () => {
  showAddForm.value = true
  editingSupplier.value = null
  resetForm()
}

const handleEditSupplier = (supplier) => {
  editingSupplier.value = supplier
  formData.value = { ...supplier }
  showAddForm.value = true
}

const handleSaveSupplier = async () => {
  isLoading.value = true
  
  try {
    if (editingSupplier.value) {
      // Update existing supplier
      const index = store.suppliers.findIndex(s => s.id === editingSupplier.value.id)
      if (index !== -1) {
        store.suppliers[index] = { ...formData.value }
      }
    } else {
      // Add new supplier
      await store.addSupplier(formData.value)
    }
    
    showAddForm.value = false
    resetForm()
  } catch (error) {
    console.error('Error saving supplier:', error)
  } finally {
    isLoading.value = false
  }
}

const handleDeleteSupplier = async (supplierId) => {
  if (confirm('Are you sure you want to delete this supplier?')) {
    const index = store.suppliers.findIndex(s => s.id === supplierId)
    if (index !== -1) {
      store.suppliers.splice(index, 1)
    }
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    contactPerson: '',
    email: '',
    phone: '',
    address: '',
    paymentTerms: '',
    rating: 0
  }
}

const getRatingStars = (rating) => {
  const stars = []
  for (let i = 1; i <= 5; i++) {
    stars.push(i <= rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300')
  }
  return stars
}
</script>

<template>
  <div class="supplier-management">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="scalingStyles.titleFontSize" class="font-bold text-gray-900 dark:text-white mb-2">
        Supplier Management
      </h1>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">
        Manage your supplier relationships and contact information
      </p>
    </div>

    <!-- Controls -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <InventoryInput
          v-model="searchTerm"
          placeholder="Search suppliers..."
          icon="fas fa-search"
        />
      </div>
      <InventoryButton
        @click="handleAddSupplier"
        variant="primary"
        size="md"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Supplier
      </InventoryButton>
    </div>

    <!-- Suppliers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="supplier in filteredSuppliers"
        :key="supplier.id"
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">{{ supplier.name }}</h3>
            <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">{{ supplier.contactPerson }}</p>
          </div>
          <StatusBadge
            :status="supplier.status"
            :text="supplier.status === 'active' ? 'Active' : 'Inactive'"
          />
        </div>

        <div class="space-y-2 mb-4">
          <div :style="scalingStyles.textFontSize" class="flex items-center text-gray-600 dark:text-gray-400">
            <i :style="scalingStyles.iconSizeSmall" class="fas fa-envelope w-4 mr-2"></i>
            {{ supplier.email }}
          </div>
          <div :style="scalingStyles.textFontSize" class="flex items-center text-gray-600 dark:text-gray-400">
            <i :style="scalingStyles.iconSizeSmall" class="fas fa-phone w-4 mr-2"></i>
            {{ supplier.phone }}
          </div>
          <div :style="scalingStyles.textFontSize" class="flex items-center text-gray-600 dark:text-gray-400">
            <i :style="scalingStyles.iconSizeSmall" class="fas fa-map-marker-alt w-4 mr-2"></i>
            {{ supplier.address }}
          </div>
          <div :style="scalingStyles.textFontSize" class="flex items-center text-gray-600 dark:text-gray-400">
            <i :style="scalingStyles.iconSizeSmall" class="fas fa-credit-card w-4 mr-2"></i>
            {{ supplier.paymentTerms }}
          </div>
        </div>

        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center">
            <span :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400 mr-2">Rating:</span>
            <div class="flex">
              <i
                v-for="(star, index) in getRatingStars(supplier.rating)"
                :key="index"
                :class="star"
                :style="scalingStyles.iconSizeSmall"
              ></i>
            </div>
          </div>
          <span :style="scalingStyles.textFontSize" class="font-medium text-gray-900 dark:text-white">
            {{ supplier.rating }}/5
          </span>
        </div>

        <div class="flex justify-end space-x-2">
          <InventoryButton
            @click="handleEditSupplier(supplier)"
            variant="ghost"
            size="sm"
          >
            <i class="fas fa-edit"></i>
          </InventoryButton>
          <InventoryButton
            @click="handleDeleteSupplier(supplier.id)"
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
        <h2 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
          {{ editingSupplier ? 'Edit Supplier' : 'Add New Supplier' }}
        </h2>
        
        <form @submit.prevent="handleSaveSupplier" class="space-y-4">
          <InventoryInput
            v-model="formData.name"
            label="Supplier Name"
            placeholder="Enter supplier name"
            required
          />
          
          <InventoryInput
            v-model="formData.contactPerson"
            label="Contact Person"
            placeholder="Enter contact person name"
            required
          />
          
          <InventoryInput
            v-model="formData.email"
            label="Email"
            type="email"
            placeholder="Enter email address"
            required
          />
          
          <InventoryInput
            v-model="formData.phone"
            label="Phone"
            placeholder="Enter phone number"
            required
          />
          
          <InventoryInput
            v-model="formData.address"
            label="Address"
            placeholder="Enter address"
            required
          />
          
          <InventoryInput
            v-model="formData.paymentTerms"
            label="Payment Terms"
            placeholder="e.g., Net 30"
            required
          />
          
          <InventoryInput
            v-model.number="formData.rating"
            label="Rating (1-5)"
            type="number"
            min="1"
            max="5"
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
              {{ editingSupplier ? 'Update' : 'Add' }} Supplier
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.supplier-management {
  padding: 0;
}
</style>
