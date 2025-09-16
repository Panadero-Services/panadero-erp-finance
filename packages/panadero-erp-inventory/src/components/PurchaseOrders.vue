<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { useScaling } from 'panadero-shared-styling'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

// State
const isLoading = ref(false)
const showAddForm = ref(false)
const editingPO = ref(null)
const searchTerm = ref('')
const statusFilter = ref('all')

// Form data
const formData = ref({
  supplierId: '',
  supplierName: '',
  expectedDelivery: '',
  items: []
})

// Computed
const filteredPOs = computed(() => {
  let pos = store.purchaseOrders
  
  if (searchTerm.value) {
    pos = pos.filter(po => 
      po.poNumber.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      po.supplierName.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (statusFilter.value !== 'all') {
    pos = pos.filter(po => po.status === statusFilter.value)
  }
  
  return pos
})

const suppliers = computed(() => 
  store.suppliers.map(s => ({ value: s.id, label: s.name }))
)

const statusOptions = [
  { value: 'all', label: 'All Statuses' },
  { value: 'draft', label: 'Draft' },
  { value: 'pending', label: 'Pending' },
  { value: 'approved', label: 'Approved' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' }
]

// Actions
const handleAddPO = () => {
  showAddForm.value = true
  editingPO.value = null
  resetForm()
}

const handleEditPO = (po) => {
  editingPO.value = po
  formData.value = { ...po }
  showAddForm.value = true
}

const handleSavePO = async () => {
  isLoading.value = true
  
  try {
    if (editingPO.value) {
      // Update existing PO
      const index = store.purchaseOrders.findIndex(po => po.id === editingPO.value.id)
      if (index !== -1) {
        store.purchaseOrders[index] = { ...formData.value }
      }
    } else {
      // Add new PO
      await store.createPurchaseOrder(formData.value)
    }
    
    showAddForm.value = false
    resetForm()
  } catch (error) {
    console.error('Error saving purchase order:', error)
  } finally {
    isLoading.value = false
  }
}

const handleDeletePO = async (poId) => {
  if (confirm('Are you sure you want to delete this purchase order?')) {
    const index = store.purchaseOrders.findIndex(po => po.id === poId)
    if (index !== -1) {
      store.purchaseOrders.splice(index, 1)
    }
  }
}

const handleApprovePO = async (poId) => {
  const po = store.purchaseOrders.find(p => p.id === poId)
  if (po) {
    po.status = 'approved'
  }
}

const resetForm = () => {
  formData.value = {
    supplierId: '',
    supplierName: '',
    expectedDelivery: '',
    items: []
  }
}

const getStatusColor = (status) => {
  const colors = {
    draft: 'gray',
    pending: 'yellow',
    approved: 'blue',
    completed: 'green',
    cancelled: 'red'
  }
  return colors[status] || 'gray'
}
</script>

<template>
  <div class="purchase-orders">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="scalingStyles.titleFontSize" class="font-bold text-gray-900 dark:text-white mb-2">
        Purchase Orders
      </h1>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">
        Manage your purchase orders and supplier relationships
      </p>
    </div>

    <!-- Controls -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <InventoryInput
          v-model="searchTerm"
          placeholder="Search purchase orders..."
          icon="fas fa-search"
        />
      </div>
      <div class="w-full sm:w-48">
        <InventoryDropdown
          v-model="statusFilter"
          :options="statusOptions"
          placeholder="Filter by status"
        />
      </div>
      <InventoryButton
        @click="handleAddPO"
        variant="primary"
        size="md"
      >
        <i class="fas fa-plus mr-2"></i>
        Create PO
      </InventoryButton>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                PO Number
              </th>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Supplier
              </th>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Order Date
              </th>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Expected Delivery
              </th>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Total Amount
              </th>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th :style="scalingStyles.smallFontSize" class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="po in filteredPOs" :key="po.id">
              <td :style="scalingStyles.smallFontSize" class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                {{ po.poNumber }}
              </td>
              <td :style="scalingStyles.smallFontSize" class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                {{ po.supplierName }}
              </td>
              <td :style="scalingStyles.smallFontSize" class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                {{ new Date(po.orderDate).toLocaleDateString() }}
              </td>
              <td :style="scalingStyles.smallFontSize" class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                {{ new Date(po.expectedDelivery).toLocaleDateString() }}
              </td>
              <td :style="scalingStyles.smallFontSize" class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                ${{ po.totalAmount.toLocaleString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge
                  :status="getStatusColor(po.status)"
                  :text="po.status.charAt(0).toUpperCase() + po.status.slice(1)"
                />
              </td>
              <td :style="scalingStyles.smallFontSize" class="px-6 py-4 whitespace-nowrap font-medium">
                <div class="flex space-x-2">
                  <InventoryButton
                    @click="handleEditPO(po)"
                    variant="ghost"
                    size="sm"
                  >
                    <i class="fas fa-edit"></i>
                  </InventoryButton>
                  <InventoryButton
                    v-if="po.status === 'pending'"
                    @click="handleApprovePO(po.id)"
                    variant="ghost"
                    size="sm"
                    class="text-green-600 hover:text-green-800"
                  >
                    <i class="fas fa-check"></i>
                  </InventoryButton>
                  <InventoryButton
                    @click="handleDeletePO(po.id)"
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
        <h2 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
          {{ editingPO ? 'Edit Purchase Order' : 'Create Purchase Order' }}
        </h2>
        
        <form @submit.prevent="handleSavePO" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryDropdown
              v-model="formData.supplierId"
              :options="suppliers"
              label="Supplier"
              placeholder="Select supplier"
              required
            />
            <InventoryInput
              v-model="formData.expectedDelivery"
              label="Expected Delivery"
              type="date"
              required
            />
          </div>
          
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
              {{ editingPO ? 'Update' : 'Create' }} PO
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.purchase-orders {
  padding: 0;
}
</style>
