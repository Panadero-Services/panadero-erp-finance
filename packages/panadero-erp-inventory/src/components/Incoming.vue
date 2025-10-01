<!--
  Incoming Component
  @version 1.0.14
  @date 17-Sep-2025
  @description Manage incoming stock orders for all product types with filtering
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
import InventoryValueCard from './ui/InventoryValueCard.vue'
import ActionButton from './ui/ActionButton.vue'
import CleanBadge from './ui/CleanBadge.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions, 
  typeOptions,
  formatCurrency,
  formatDate,
  confirmAction 
} = useCommonSnippets()

// Get ERP data
const {
  ordersIn: erpOrdersIn,
  orderInLines: erpOrderInLines,
  suppliers: erpSuppliers,
  products: erpProducts,
  sites: erpSites,
  statuses: erpStatuses,
  isLoading: erpLoading,
  fetchOrdersIn,
  fetchOrderInLines,
  fetchSuppliers,
  fetchProducts,
  fetchSites,
  fetchStatuses,
  getSupplierName,
  getProductName,
  getSiteName,
  getStatusName,
  getStatusColor
} = useErpData()

// State
const selectedProductType = ref('items') // Filter: items, bulk, liquids
const searchTerm = ref('')
const selectedStatus = ref('all')
const showOrderForm = ref(false)
const editingOrder = ref(null)
const orderFormData = ref({
  order_number: '',
  supplier: '',
  expected_date: '',
  status: 'planned',
  notes: ''
})

// Use real ERP data for incoming orders
const incomingOrders = computed(() => {
  return erpOrdersIn.value.map(order => {
    const orderLines = erpOrderInLines.value.filter(line => line.order_in_id === order.id)
    return {
      id: order.id,
      order_number: order.ordernr,
      supplier: getSupplierName(order.supplier_id),
      supplier_id: order.supplier_id,
      site_id: order.site_id,
      status_id: order.status_id,
      status: getStatusName(order.status_id),
      notes: order.comment || '',
      created_at: order.created_at,
      updated_at: order.updated_at,
      // For display purposes, we'll show the first order line or create a summary
      product_type: orderLines.length > 0 ? getProductTypeName(erpProducts.value.find(p => p.id === orderLines[0].product_id)?.product_type_id) || 'general' : 'general',
      product_sku: orderLines.length > 0 ? getProductName(orderLines[0].product_id) : 'Multiple Items',
      product_name: orderLines.length > 0 ? getProductName(orderLines[0].product_id) : 'Multiple Products',
      quantity: orderLines.reduce((sum, line) => sum + (line.qty || 0), 0),
      unit: 'units',
      unit_cost: orderLines.length > 0 ? orderLines[0].unit_cost || 0 : 0,
      total_cost: orderLines.reduce((sum, line) => sum + ((line.qty || 0) * (line.unit_cost || 0)), 0),
      expected_date: order.expected_date || '',
      actual_date: order.actual_date || null
    }
  })
})

const productTypeOptions = [
  { value: 'items', label: 'Discrete Items' },
  { value: 'bulk', label: 'Bulk Materials' },
  { value: 'liquids', label: 'Liquids' }
]

const orderStatusOptions = [
  { value: 'all', label: 'All Status' },
  { value: 'planned', label: 'Planned' },
  { value: 'in_transit', label: 'In Transit' },
  { value: 'received', label: 'Received' },
  { value: 'cancelled', label: 'Cancelled' }
]

// Computed properties
const currentOrders = computed(() => {
  return incomingOrders.value.filter(order => order.product_type === selectedProductType.value)
})

const filteredOrders = computed(() => {
  let filtered = [...currentOrders.value]
  
  if (searchTerm.value) {
    filtered = filtered.filter(order => 
      order.order_number.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      order.product_name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      order.supplier.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (selectedStatus.value !== 'all') {
    filtered = filtered.filter(order => order.status === selectedStatus.value)
  }
  
  return filtered
})

const ordersByStatus = computed(() => {
  const statuses = ['planned', 'in_transit', 'received', 'cancelled']
  return statuses.reduce((acc, status) => {
    acc[status] = currentOrders.value.filter(order => order.status === status).length
    return acc
  }, {})
})

const totalValue = computed(() => {
  return currentOrders.value.reduce((total, order) => total + order.total_cost, 0)
})

// getStatusColor is already imported from useErpData

const getProductTypeIcon = (type) => {
  switch (type) {
    case 'items': return 'fas fa-box'
    case 'bulk': return 'fas fa-weight-hanging'
    case 'liquids': return 'fas fa-tint'
    default: return 'fas fa-cube'
  }
}

// Methods
const openOrderForm = (order = null) => {
  editingOrder.value = order
  orderFormData.value = order ? { ...order } : {
    order_number: '',
    supplier: '',
    expected_date: '',
    status: 'planned',
    notes: ''
  }
  showOrderForm.value = true
}

const saveOrder = () => {
  if (editingOrder.value) {
    // Update existing order
    const index = incomingOrders.value.findIndex(o => o.id === editingOrder.value.id)
    if (index !== -1) {
      incomingOrders.value[index] = { ...orderFormData.value, id: editingOrder.value.id }
    }
  } else {
    // Add new order
    const newOrder = {
      ...orderFormData.value,
      id: Date.now(),
      product_type: selectedProductType.value,
      product_sku: '',
      product_name: '',
      quantity: 0,
      unit: selectedProductType.value === 'items' ? 'units' : selectedProductType.value === 'bulk' ? 'kg' : 'liters',
      unit_cost: 0,
      total_cost: 0,
      actual_date: null,
      created_at: new Date().toISOString().split('T')[0]
    }
    incomingOrders.value.push(newOrder)
  }
  showOrderForm.value = false
}

const handleDeleteOrder = (id) => {
  if (confirmAction('Are you sure you want to delete this order?')) {
    const index = incomingOrders.value.findIndex(o => o.id === id)
    if (index !== -1) {
      incomingOrders.value.splice(index, 1)
    }
  }
}

const updateOrderStatus = (id, status) => {
  const order = incomingOrders.value.find(o => o.id === id)
  if (order) {
    order.status = status
    if (status === 'received') {
      order.actual_date = new Date().toISOString().split('T')[0]
    }
  }
}

onMounted(() => {
  store.loadSettings()
  // Fetch ERP data
  fetchOrdersIn()
  fetchOrderInLines()
  fetchSuppliers()
  fetchProducts()
  fetchSites()
  fetchStatuses()
})
</script>

<template>
  <div class="incoming" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Incoming Stock
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Manage incoming stock orders for all product types
      </p>
    </div>

    <!-- Product Type Filter -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border mb-6']">
      <div class="p-4">
        <div class="flex items-center gap-4">
          <label :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="font-medium">
            Product Type:
          </label>
          <InventoryDropdown
            v-model="selectedProductType"
            :options="productTypeOptions"
            :style="scalingStyles.select"
            :class="darkModeClasses.input"
            class="w-48"
          />
          <div class="flex items-center gap-2">
              <i :class="[getProductTypeIcon(selectedProductType), darkModeClasses.text]" :style="scalingStyles.iconSize"></i>
            <span :style="scalingStyles.textFontSize" :class="darkModeClasses.text">
              {{ productTypeOptions.find(opt => opt.value === selectedProductType)?.label }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Planned Orders"
        :value="ordersByStatus.planned"
        subtitle="Scheduled"
        icon="fas fa-calendar"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="In Transit"
        :value="ordersByStatus.in_transit"
        subtitle="On the Way"
        icon="fas fa-truck"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Received"
        :value="ordersByStatus.received"
        subtitle="Completed"
        icon="fas fa-check-circle"
        color="green"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Total Value"
        :value="formatCurrency(totalValue)"
        subtitle="Pending Orders"
        icon="fas fa-dollar-sign"
        color="purple"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Filters -->
    <div :class="[darkModeClasses.card, 'p-4 rounded-lg shadow-sm border mb-6']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <InventoryInput
          v-model="searchTerm"
          placeholder="Search orders..."
          :style="scalingStyles.input"
          :class="darkModeClasses.input"
        >
          <template #prefix>
            <i class="fas fa-search"></i>
          </template>
        </InventoryInput>

        <InventoryDropdown
          v-model="selectedStatus"
          :options="orderStatusOptions"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <InventoryButton
          @click="openOrderForm()"
          variant="primary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-plus mr-2"></i>
          New Order
        </InventoryButton>

        <InventoryButton
          @click="updateOrderStatus"
          variant="secondary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-edit mr-2"></i>
          Update Status
        </InventoryButton>
      </div>
    </div>

    <!-- Orders Table -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border overflow-hidden']">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Order #</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Product</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Supplier</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Quantity</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Expected Date</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="order in filteredOrders" :key="order.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                {{ order.order_number }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <div :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm font-medium">{{ order.product_name }}</div>
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm">{{ order.product_sku }}</div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ order.supplier }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ order.quantity }} {{ order.unit }}
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
                  {{ formatCurrency(order.total_cost) }}
                </div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatDate(order.expected_date) }}
                <div v-if="order.actual_date" :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
                  Received: {{ formatDate(order.actual_date) }}
                </div>
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <CleanBadge 
                  :variant="getStatusColor(order.status)" 
                  :text="order.status.charAt(0).toUpperCase() + order.status.slice(1).replace('_', ' ')" 
                  size="xs" 
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton variant="edit" :iconStyle="scalingStyles.iconSize" @click="openOrderForm(order)" />
                  <ActionButton variant="delete" :iconStyle="scalingStyles.iconSize" @click="handleDeleteOrder(order.id)" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Form Modal -->
    <div v-if="showOrderForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            {{ editingOrder ? 'Edit Order' : 'Add New Order' }}
          </h3>
          <button @click="showOrderForm = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveOrder" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="orderFormData.order_number"
              label="Order Number"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="orderFormData.supplier"
              label="Supplier"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="orderFormData.expected_date"
              label="Expected Date"
              type="date"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryDropdown
              v-model="orderFormData.status"
              label="Status"
              :options="orderStatusOptions.filter(opt => opt.value !== 'all')"
              required
              :style="scalingStyles.select"
              :class="darkModeClasses.input"
            />
          </div>

          <InventoryInput
            v-model="orderFormData.notes"
            label="Notes"
            type="textarea"
            :style="scalingStyles.textarea"
            :class="darkModeClasses.input"
          />

          <div class="flex justify-end gap-2 pt-4">
            <InventoryButton @click="showOrderForm = false" variant="secondary" :style="scalingStyles.button">
              Cancel
            </InventoryButton>
            <InventoryButton type="submit" variant="primary" :style="scalingStyles.button">
              {{ editingOrder ? 'Update' : 'Create' }}
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>