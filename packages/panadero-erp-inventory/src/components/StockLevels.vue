<!--
  Stock Levels Component
  @version 1.0.13
  @date 17-Sep-2025
  @description Current stock levels and status for all products
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStockLevels } from '../composables/useStockLevels.js'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import InventoryValueCard from './ui/InventoryValueCard.vue'
import ActionButton from './ui/ActionButton.vue'

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
const { 
  stockLevels, 
  getStockByStatus, 
  getLowStockItems, 
  getStockValuation,
  updateStockLevel,
  adjustStock 
} = useStockLevels()

// State
const searchTerm = ref('')
const selectedStatus = ref('all')
const selectedType = ref('all')
const showAdjustmentForm = ref(false)
const selectedStock = ref(null)
const adjustmentFormData = ref({
  quantity: 0,
  type: 'increase',
  reason: ''
})

// Options are now provided by useCommonSnippets

const adjustmentTypeOptions = [
  { value: 'increase', label: 'Increase Stock' },
  { value: 'decrease', label: 'Decrease Stock' }
]

// Computed properties
const outOfStockItems = computed(() => getStockByStatus.value('out'))
const lowStockItems = computed(() => getLowStockItems.value)
const stockValuation = computed(() => getStockValuation.value)

const filteredStock = computed(() => {
  let filtered = [...stockLevels.value]
  
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

// Dark mode classes are now provided by useCommonSnippets

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
    
    adjustStock(selectedStock.value.id, adjustment, adjustmentFormData.value.reason)
  }
  showAdjustmentForm.value = false
}

const viewStockHistory = (stock) => {
  console.log('View stock history for:', stock.sku)
}

// formatCurrency is now provided by useCommonSnippets

onMounted(() => {
  store.loadSettings()
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
        Current stock levels and status for all products
      </p>
    </div>

    <!-- Stock Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Total Items"
        :value="stockLevels.length"
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
          :options="statusOptions"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <InventoryDropdown
          v-model="selectedType"
          :options="typeOptions"
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
                <div :style="scalingStyles.textFontSizeSubtext" :class="darkModeClasses.textSecondary" class="text-sm">{{ stock.unit }}</div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ stock.currentStock }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ stock.minStock }} / {{ stock.maxStock }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ stock.location }} {{ stock.zone ? `- ${stock.zone}` : '' }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <StatusBadge 
                  :status="stock.status === 'normal' ? 'success' : stock.status === 'low' ? 'warning' : 'danger'"
                  :text="stock.status === 'normal' ? 'Normal' : stock.status === 'low' ? 'Low Stock' : 'Out of Stock'"
                  :scaling-styles="scalingStyles"
                  :dark-mode="store.settings.darkMode"
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton  variant="edit" @click="openAdjustmentForm(stock)" />
                  <ActionButton  variant="view" @click="viewStockHistory(stock)" />
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
              Current Stock: {{ selectedStock.currentStock }} {{ selectedStock.unit }}
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


