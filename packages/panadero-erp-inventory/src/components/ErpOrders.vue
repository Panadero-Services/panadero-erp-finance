<!--
  ERP Orders Component
  @version 1.0.0
  @date 19-Sep-2025
  @description Displays ERP orders (incoming and outgoing) with real data from erp_ tables
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'
import { useErpData } from '../composables/useErpData.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import ActionButton from './ui/ActionButton.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions: commonStatusOptions, 
  typeOptions,
  formatCurrency,
  formatDate,
  confirmAction 
} = useCommonSnippets()

// Get ERP data
const {
  ordersIn: erpOrdersIn,
  ordersOut: erpOrdersOut,
  orderInLines: erpOrderInLines,
  orderOutLines: erpOrderOutLines,
  customers: erpCustomers,
  suppliers: erpSuppliers,
  products: erpProducts,
  sites: erpSites,
  statuses: erpStatuses,
  isLoading: erpLoading,
  fetchOrdersIn,
  fetchOrdersOut,
  fetchOrderInLines,
  fetchOrderOutLines,
  fetchCustomers,
  fetchSuppliers,
  fetchProducts,
  fetchSites,
  fetchStatuses,
  getCustomerName,
  getSupplierName,
  getProductName,
  getSiteName,
  getStatusName,
  getStatusColor
} = useErpData()

// State
const activeTab = ref('incoming')
const searchTerm = ref('')
const statusFilter = ref('all')
const siteFilter = ref('all')
const showOrderForm = ref(false)
const editingOrder = ref(null)
const selectedOrder = ref(null)
const showOrderLines = ref(false)

// Form data
const orderFormData = ref({
  ordernr: '',
  supplier_id: null,
  customer_id: null,
  site_id: null,
  status_id: null,
  comment: ''
})

// Computed properties
const filteredOrdersIn = computed(() => {
  let orders = erpOrdersIn.value
  
  if (searchTerm.value) {
    orders = orders.filter(order => 
      order.ordernr.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      getSupplierName(order.supplier_id).toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (statusFilter.value !== 'all') {
    orders = orders.filter(order => order.status_id === parseInt(statusFilter.value))
  }
  
  if (siteFilter.value !== 'all') {
    orders = orders.filter(order => order.site_id === parseInt(siteFilter.value))
  }
  
  return orders
})

const filteredOrdersOut = computed(() => {
  let orders = erpOrdersOut.value
  
  if (searchTerm.value) {
    orders = orders.filter(order => 
      order.ordernr.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      getCustomerName(order.customer_id).toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (statusFilter.value !== 'all') {
    orders = orders.filter(order => order.status_id === parseInt(statusFilter.value))
  }
  
  if (siteFilter.value !== 'all') {
    orders = orders.filter(order => order.site_id === parseInt(siteFilter.value))
  }
  
  return orders
})

const orderLinesForOrder = computed(() => {
  if (!selectedOrder.value) return []
  
  if (activeTab.value === 'incoming') {
    return erpOrderInLines.value.filter(line => line.order_in_id === selectedOrder.value.id)
  } else {
    return erpOrderOutLines.value.filter(line => line.order_out_id === selectedOrder.value.id)
  }
})

const erpSupplierOptions = computed(() => 
  erpSuppliers.value.map(s => ({ value: s.id, label: s.name }))
)

const erpCustomerOptions = computed(() => 
  erpCustomers.value.map(c => ({ value: c.id, label: c.name }))
)

const erpSiteOptions = computed(() => 
  erpSites.value.map(s => ({ value: s.id, label: s.name }))
)

const erpStatusOptions = computed(() => [
  { value: 'all', label: 'All Statuses' },
  ...erpStatuses.value.map(s => ({ value: s.id, label: s.name }))
])

const erpSiteFilterOptions = computed(() => [
  { value: 'all', label: 'All Sites' },
  ...erpSites.value.map(s => ({ value: s.id, label: s.name }))
])

// Actions
const handleAddOrder = () => {
  showOrderForm.value = true
  editingOrder.value = null
  resetForm()
}

const handleEditOrder = (order) => {
  editingOrder.value = order
  orderFormData.value = { ...order }
  showOrderForm.value = true
}

const handleViewOrder = (order) => {
  selectedOrder.value = order
  showOrderLines.value = true
}

const handleSaveOrder = async () => {
  // Implementation for saving order
  console.log('Saving order:', orderFormData.value)
  showOrderForm.value = false
  resetForm()
}

const handleDeleteOrder = async (order) => {
  if (confirmAction(`Are you sure you want to delete order ${order.ordernr}?`)) {
    // Implementation for deleting order
    console.log('Deleting order:', order.id)
  }
}

const resetForm = () => {
  orderFormData.value = {
    ordernr: '',
    supplier_id: null,
    customer_id: null,
    site_id: null,
    status_id: null,
    comment: ''
  }
}

const closeOrderLines = () => {
  showOrderLines.value = false
  selectedOrder.value = null
}

// Auto-fetch data on mount
onMounted(() => {
  fetchOrdersIn()
  fetchOrdersOut()
  fetchOrderInLines()
  fetchOrderOutLines()
  fetchCustomers()
  fetchSuppliers()
  fetchProducts()
  fetchSites()
  fetchStatuses()
})
</script>

<template>
  <div :class="darkModeClasses.container" class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold mb-2">ERP Orders Management</h2>
      <p class="text-gray-600">Manage incoming and outgoing orders with real ERP data</p>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'incoming'"
            :class="[
              activeTab === 'incoming' 
                ? 'border-blue-500 text-blue-600' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Incoming Orders ({{ erpOrdersIn.length }})
          </button>
          <button
            @click="activeTab = 'outgoing'"
            :class="[
              activeTab === 'outgoing' 
                ? 'border-blue-500 text-blue-600' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Outgoing Orders ({{ erpOrdersOut.length }})
          </button>
        </nav>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
      <InventoryInput
        v-model="searchTerm"
        placeholder="Search orders..."
        class="w-full"
      />
      <InventoryDropdown
        v-model="statusFilter"
        :options="erpStatusOptions"
        placeholder="Filter by status"
        class="w-full"
      />
      <InventoryDropdown
        v-model="siteFilter"
        :options="erpSiteFilterOptions"
        placeholder="Filter by site"
        class="w-full"
      />
      <InventoryButton
        @click="handleAddOrder"
        class="w-full"
      >
        Add New Order
      </InventoryButton>
    </div>

    <!-- ERP Data Status -->
    <div v-if="erpOrdersIn.length > 0 || erpOrdersOut.length > 0" class="mb-4 p-4 bg-green-100 border border-green-400 rounded">
      <h3 class="font-bold text-green-800">✅ ERP Data Connected</h3>
      <p class="text-green-600">
        Displaying {{ erpOrdersIn.length }} incoming orders and {{ erpOrdersOut.length }} outgoing orders from ERP system.
      </p>
    </div>

    <!-- Orders Table -->
    <div :class="darkModeClasses.card" class="rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Order #</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                {{ activeTab === 'incoming' ? 'Supplier' : 'Customer' }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Site</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Created</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="(activeTab === 'incoming' ? filteredOrdersIn : filteredOrdersOut).length === 0" 
                :class="darkModeClasses.tableRow">
              <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                No orders found. {{ erpLoading ? 'Loading...' : 'Try refreshing the page.' }}
              </td>
            </tr>
            <tr v-for="order in (activeTab === 'incoming' ? filteredOrdersIn : filteredOrdersOut)" 
                :key="order.id" 
                :class="darkModeClasses.tableRow">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                {{ order.ordernr }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ activeTab === 'incoming' ? getSupplierName(order.supplier_id) : getCustomerName(order.customer_id) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ getSiteName(order.site_id) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge 
                  :status="getStatusName(order.status_id)"
                  :color="getStatusColor(order.status_id)"
                />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ order.created_at ? formatDate(order.created_at) : 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <ActionButton
                  @click="handleViewOrder(order)"
                  variant="view"
                  size="sm"
                >
                  View Lines
                </ActionButton>
                <ActionButton
                  @click="handleEditOrder(order)"
                  variant="edit"
                  size="sm"
                >
                  Edit
                </ActionButton>
                <ActionButton
                  @click="handleDeleteOrder(order)"
                  variant="delete"
                  size="sm"
                >
                  Delete
                </ActionButton>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Lines Modal -->
    <div v-if="showOrderLines" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">
              Order Lines - {{ selectedOrder?.ordernr }}
            </h3>
            <button @click="closeOrderLines" class="text-gray-400 hover:text-gray-600">
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="line in orderLinesForOrder" :key="line.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{ getProductName(line.product_id) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{ line.qty }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{ getSiteName(line.site_id) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <StatusBadge 
                      :status="getStatusName(line.status_id)"
                      :color="getStatusColor(line.status_id)"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="erpLoading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Loading ERP data...</p>
    </div>
  </div>
</template>
