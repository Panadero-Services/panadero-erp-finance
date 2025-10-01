<!--
  Stock Levels Component
  @version 1.0.14
  @date 17-Sep-2025
  @description Current stock levels and status for all product types with separate tracking
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
  confirmAction 
} = useCommonSnippets()

// Get ERP data
const {
  products: erpProducts,
  stocks: erpStocks,
  sites: erpSites,
  statuses: erpStatuses,
  isLoading: erpLoading,
  fetchProducts,
  fetchStocks,
  fetchSites,
  fetchStatuses,
  getProductName,
  getSiteName,
  getStatusName,
  getStatusColor
} = useErpData()

// State
const searchTerm = ref('')
const selectedStatus = ref('all')
const selectedProductType = ref('items') // Filter: items, bulk, liquids
const showAdjustmentForm = ref(false)
const selectedStock = ref(null)
const adjustmentFormData = ref({
  quantity: 0,
  type: 'increase',
  reason: ''
})

// Use real ERP data for item stock
const itemStock = computed(() => {
  return erpStocks.value
    .filter(stock => stock.product_id && erpProducts.value.find(p => p.id === stock.product_id))
    .map(stock => {
      const product = erpProducts.value.find(p => p.id === stock.product_id)
      return {
        id: stock.id,
        item_id: stock.product_id,
        location_id: stock.site_id,
        current_units: stock.qty || 0,
        reserved_units: stock.reserved_qty || 0,
        available_units: (stock.qty || 0) - (stock.reserved_qty || 0),
        min_units: product?.min_stock_units || 0,
        max_units: product?.max_stock_units || 0,
        reorder_point_units: product?.reorder_point_units || 0,
        reorder_quantity_units: product?.reorder_quantity_units || 0,
        unit_cost: product?.unit_cost || 0,
        total_value: (stock.qty || 0) * (product?.unit_cost || 0),
        average_cost: product?.unit_cost || 0,
        status: getStatusName(stock.status_id) || 'active',
        last_counted: stock.last_counted || '',
        last_movement: stock.last_movement || '',
        // Product details
        sku: product?.code || `ITEM-${stock.product_id}`,
        name: getProductName(stock.product_id),
        category: product?.category || 'General',
        brand: product?.brand || '',
        location: getSiteName(stock.site_id),
        unit: 'units'
      }
    })
})

// Use real ERP data for bulk stock
const bulkStock = computed(() => {
  return erpStocks.value
    .filter(stock => {
      const product = erpProducts.value.find(p => p.id === stock.product_id)
      return product && product.product_type_id && getProductTypeName(product.product_type_id)?.toLowerCase().includes('bulk')
    })
    .map(stock => {
      const product = erpProducts.value.find(p => p.id === stock.product_id)
      return {
        id: stock.id,
        material_id: stock.product_id,
        location_id: stock.site_id,
        current_kg: stock.qty || 0,
        reserved_kg: stock.reserved_qty || 0,
        available_kg: (stock.qty || 0) - (stock.reserved_qty || 0),
        min_kg: product?.min_stock_kg || 0,
        max_kg: product?.max_stock_kg || 0,
        reorder_point_kg: product?.reorder_point_kg || 0,
        reorder_quantity_kg: product?.reorder_quantity_kg || 0,
        cost_per_kg: product?.cost_per_kg || 0,
        total_value: (stock.qty || 0) * (product?.cost_per_kg || 0),
        average_cost_per_kg: product?.cost_per_kg || 0,
        quality_status: 'good',
        status: getStatusName(stock.status_id) || 'active',
        last_counted: stock.last_counted || '',
        last_movement: stock.last_movement || '',
        // Product details
        sku: product?.code || `BULK-${stock.product_id}`,
        name: getProductName(stock.product_id),
        category: product?.category || 'Raw Materials',
        brand: product?.brand || '',
        location: getSiteName(stock.site_id),
        unit: 'kg'
      }
    })
})

// Use real ERP data for liquid stock
const liquidStock = computed(() => {
  return erpStocks.value
    .filter(stock => {
      const product = erpProducts.value.find(p => p.id === stock.product_id)
      return product && product.product_type_id && getProductTypeName(product.product_type_id)?.toLowerCase().includes('liquid')
    })
    .map(stock => {
      const product = erpProducts.value.find(p => p.id === stock.product_id)
      return {
        id: stock.id,
        liquid_id: stock.product_id,
        location_id: stock.site_id,
        current_liters: stock.qty || 0,
        reserved_liters: stock.reserved_qty || 0,
        available_liters: (stock.qty || 0) - (stock.reserved_qty || 0),
        min_liters: product?.min_stock_liters || 0,
        max_liters: product?.max_stock_liters || 0,
        reorder_point_liters: product?.reorder_point_liters || 0,
        reorder_quantity_liters: product?.reorder_quantity_liters || 0,
        cost_per_liter: product?.cost_per_liter || 0,
        total_value: (stock.qty || 0) * (product?.cost_per_liter || 0),
        average_cost_per_liter: product?.cost_per_liter || 0,
        quality_status: 'good',
        status: getStatusName(stock.status_id) || 'active',
        last_counted: stock.last_counted || '',
        last_movement: stock.last_movement || '',
        // Product details
        sku: product?.code || `LIQ-${stock.product_id}`,
        name: getProductName(stock.product_id),
        category: product?.category || 'Raw Materials',
        brand: product?.brand || '',
        location: getSiteName(stock.site_id),
        unit: 'liters'
      }
    })
})

// Computed properties
const currentStock = computed(() => {
  switch (selectedProductType.value) {
    case 'items':
      return itemStock.value
    case 'bulk':
      return bulkStock.value
    case 'liquids':
      return liquidStock.value
    default:
      return []
  }
})

const productTypeOptions = [
  { value: 'items', label: 'Discrete Items' },
  { value: 'bulk', label: 'Bulk Materials' },
  { value: 'liquids', label: 'Liquids' }
]

const stockStatusOptions = [
  { value: 'all', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'low_stock', label: 'Low Stock' },
  { value: 'out_of_stock', label: 'Out of Stock' }
]

const adjustmentTypeOptions = [
  { value: 'increase', label: 'Increase Stock' },
  { value: 'decrease', label: 'Decrease Stock' }
]

const outOfStockItems = computed(() => 
  currentStock.value.filter(stock => stock.status === 'out_of_stock')
)

const lowStockItems = computed(() => 
  currentStock.value.filter(stock => stock.status === 'low_stock')
)

const stockValuation = computed(() => {
  return currentStock.value.reduce((total, stock) => total + (stock.total_value || 0), 0)
})

const filteredStock = computed(() => {
  let filtered = [...currentStock.value]
  
  if (searchTerm.value) {
    filtered = filtered.filter(stock => 
      stock.sku.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      stock.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (selectedStatus.value !== 'all') {
    filtered = filtered.filter(stock => stock.status === selectedStatus.value)
  }
  
  return filtered
})

const getStockStatus = (stock) => {
  if (stock.status === 'out_of_stock') return 'danger'
  if (stock.status === 'low_stock') return 'warning'
  return 'success'
}

const getStockStatusText = (stock) => {
  if (stock.status === 'out_of_stock') return 'Out of Stock'
  if (stock.status === 'low_stock') return 'Low Stock'
  return 'Normal'
}

const getProductTypeIcon = (type) => {
  switch (type) {
    case 'items':
      return 'fas fa-box'
    case 'bulk':
      return 'fas fa-weight-hanging'
    case 'liquids':
      return 'fas fa-tint'
    default:
      return 'fas fa-cube'
  }
}

const getCurrentStockValue = (stock) => {
  switch (selectedProductType.value) {
    case 'items':
      return `${stock.current_units} ${stock.unit}`
    case 'bulk':
      return `${stock.current_kg} ${stock.unit}`
    case 'liquids':
      return `${stock.current_liters} ${stock.unit}`
    default:
      return '0'
  }
}

const getMinMaxValue = (stock) => {
  switch (selectedProductType.value) {
    case 'items':
      return `${stock.min_units} / ${stock.max_units}`
    case 'bulk':
      return `${stock.min_kg} / ${stock.max_kg}`
    case 'liquids':
      return `${stock.min_liters} / ${stock.max_liters}`
    default:
      return '0 / 0'
  }
}

// Methods
const openAdjustmentForm = (stock = null) => {
  selectedStock.value = stock
  adjustmentFormData.value = {
    quantity: 0,
    type: 'increase',
    reason: ''
  }
  showAdjustmentForm.value = true
}

const saveAdjustment = () => {
  if (selectedStock.value) {
    const adjustment = adjustmentFormData.value.type === 'increase' 
      ? adjustmentFormData.value.quantity 
      : -adjustmentFormData.value.quantity
    
    // Update the stock level
    const index = currentStock.value.findIndex(s => s.id === selectedStock.value.id)
    if (index !== -1) {
      switch (selectedProductType.value) {
        case 'items':
          currentStock.value[index].current_units += adjustment
          currentStock.value[index].available_units += adjustment
          break
        case 'bulk':
          currentStock.value[index].current_kg += adjustment
          currentStock.value[index].available_kg += adjustment
          break
        case 'liquids':
          currentStock.value[index].current_liters += adjustment
          currentStock.value[index].available_liters += adjustment
          break
      }
      
      // Update status based on new levels
      updateStockStatus(currentStock.value[index])
    }
  }
  showAdjustmentForm.value = false
}

const updateStockStatus = (stock) => {
  let current, min, max
  switch (selectedProductType.value) {
    case 'items':
      current = stock.current_units
      min = stock.min_units
      max = stock.max_units
      break
    case 'bulk':
      current = stock.current_kg
      min = stock.min_kg
      max = stock.max_kg
      break
    case 'liquids':
      current = stock.current_liters
      min = stock.min_liters
      max = stock.max_liters
      break
  }
  
  if (current <= 0) {
    stock.status = 'out_of_stock'
  } else if (current <= min) {
    stock.status = 'low_stock'
  } else {
    stock.status = 'active'
  }
}

const viewStockHistory = (stock) => {
  console.log('View stock history for:', stock.sku)
}

onMounted(() => {
  store.loadSettings()
  // Fetch ERP data
  fetchProducts()
  fetchStocks()
  fetchSites()
  fetchStatuses()
})
</script>

<template>
  <div class="stock-levels" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Stock Levels
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Current stock levels and status for all product types
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

    <!-- Stock Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Total Items"
        :value="currentStock.length"
        subtitle="All Products"
        icon="fas fa-boxes"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Low Stock"
        :value="lowStockItems.length"
        subtitle="Need Reorder"
        icon="fas fa-exclamation-triangle"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Out of Stock"
        :value="outOfStockItems.length"
        subtitle="Critical"
        icon="fas fa-times-circle"
        color="red"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Total Value"
        :value="formatCurrency(stockValuation)"
        subtitle="Inventory Value"
        icon="fas fa-dollar-sign"
        color="green"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Filters -->
    <div :class="[darkModeClasses.card, 'p-4 rounded-lg shadow-sm border mb-6']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <InventoryInput
          v-model="searchTerm"
          placeholder="Search products..."
          :style="scalingStyles.input"
          :class="darkModeClasses.input"
        >
          <template #prefix>
            <i class="fas fa-search"></i>
          </template>
        </InventoryInput>

        <InventoryDropdown
          v-model="selectedStatus"
          :options="stockStatusOptions"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <InventoryButton
          @click="showAdjustmentForm = true"
          variant="primary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-edit mr-2"></i>
          Adjust Stock
        </InventoryButton>

        <InventoryButton
          @click="viewStockHistory"
          variant="secondary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-history mr-2"></i>
          View History
        </InventoryButton>
      </div>
    </div>

    <!-- Stock Levels Table -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border overflow-hidden']">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">SKU</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Product</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Current Stock</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Min/Max</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Location</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="stock in filteredStock" :key="stock.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                {{ stock.sku }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <div :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm font-medium">{{ stock.name }}</div>
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm">{{ stock.category }} - {{ stock.brand }}</div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ getCurrentStockValue(stock) }}
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
                  Available: {{ selectedProductType === 'items' ? stock.available_units : selectedProductType === 'bulk' ? stock.available_kg : stock.available_liters }}
                </div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ getMinMaxValue(stock) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ stock.location }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <CleanBadge 
                  :variant="getStockStatus(stock) === 'success' ? 'green' : getStockStatus(stock) === 'warning' ? 'orange' : 'red'" 
                  :text="getStockStatusText(stock)" 
                  size="xs" 
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton variant="edit" :iconStyle="scalingStyles.iconSize" @click="openAdjustmentForm(stock)" />
                  <ActionButton variant="view" :iconStyle="scalingStyles.iconSize" @click="viewStockHistory(stock)" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div v-if="showAdjustmentForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-md']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            Adjust Stock Level
          </h3>
          <button @click="showAdjustmentForm = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveAdjustment" class="space-y-4">
          <div v-if="selectedStock">
            <label :style="scalingStyles.label" :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
              Product: {{ selectedStock.name }} ({{ selectedStock.sku }})
            </label>
            <label :style="scalingStyles.label" :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
              Current Stock: {{ getCurrentStockValue(selectedStock) }}
            </label>
          </div>

          <InventoryInput
            v-model.number="adjustmentFormData.quantity"
            label="Adjustment Quantity"
            type="number"
            required
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />

          <InventoryDropdown
            v-model="adjustmentFormData.type"
            label="Adjustment Type"
            :options="adjustmentTypeOptions"
            required
            :style="scalingStyles.select"
            :class="darkModeClasses.input"
          />

          <InventoryInput
            v-model="adjustmentFormData.reason"
            label="Reason"
            required
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />

          <div class="flex justify-end gap-2 pt-4">
            <InventoryButton @click="showAdjustmentForm = false" variant="secondary" :style="scalingStyles.button">
              Cancel
            </InventoryButton>
            <InventoryButton type="submit" variant="primary" :style="scalingStyles.button">
              Apply Adjustment
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>