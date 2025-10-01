<!--
  Closeout Component
  @version 1.0.14
  @date 17-Sep-2025
  @description Unified inventory closeout reports for all product types (items, bulk, liquids)
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

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

// State
const showCloseoutDetails = ref(false)
const selectedCloseout = ref(null)
const selectedPeriod = ref('monthly')
const selectedDateRange = ref({
  start: '',
  end: ''
})

// Mock data for unified closeout reports
const closeoutPeriods = ref([
  {
    id: 1,
    period: '2025-09',
    type: 'monthly',
    startDate: '2025-09-01',
    endDate: '2025-09-30',
    status: 'completed',
    totalStartValue: 15420.50,
    totalIncomingValue: 3250.75,
    totalOutgoingValue: 2890.25,
    totalAdjustmentsValue: 150.00,
    totalEndValue: 15931.00,
    totalVariance: 510.50,
    products: [
      // Items
      {
        sku: 'ITEM-BREAD-001',
        name: 'Premium Artisan Bread',
        type: 'items',
        category: 'Bakery',
        startStock: '45 units',
        incoming: '200 units',
        outgoing: '180 units',
        adjustments: '5 units',
        endStock: '70 units',
        variance: '+25 units',
        status: 'balanced',
        startValue: 157.50,
        incomingValue: 1050.00,
        outgoingValue: 945.00,
        adjustmentsValue: 17.50,
        endValue: 280.00,
        varianceValue: 122.50
      },
      {
        sku: 'ITEM-TOOL-001',
        name: 'Professional Mixing Bowl Set',
        type: 'items',
        category: 'Tools',
        startStock: '8 units',
        incoming: '10 units',
        outgoing: '5 units',
        adjustments: '0 units',
        endStock: '13 units',
        variance: '+5 units',
        status: 'balanced',
        startValue: 360.00,
        incomingValue: 450.00,
        outgoingValue: 225.00,
        adjustmentsValue: 0.00,
        endValue: 585.00,
        varianceValue: 225.00
      },
      // Bulk Materials
      {
        sku: 'BULK-FLOUR-001',
        name: 'Organic Whole Wheat Flour',
        type: 'bulk',
        category: 'Raw Materials',
        startStock: '125.5 kg',
        incoming: '200.0 kg',
        outgoing: '150.0 kg',
        adjustments: '0.0 kg',
        endStock: '175.5 kg',
        variance: '+50.0 kg',
        status: 'balanced',
        startValue: 282.38,
        incomingValue: 450.00,
        outgoingValue: 337.50,
        adjustmentsValue: 0.00,
        endValue: 394.88,
        varianceValue: 112.50
      },
      {
        sku: 'BULK-SALT-001',
        name: 'Sea Salt',
        type: 'bulk',
        category: 'Raw Materials',
        startStock: '25.0 kg',
        incoming: '50.0 kg',
        outgoing: '30.0 kg',
        adjustments: '0.0 kg',
        endStock: '45.0 kg',
        variance: '+20.0 kg',
        status: 'balanced',
        startValue: 31.25,
        incomingValue: 62.50,
        outgoingValue: 37.50,
        adjustmentsValue: 0.00,
        endValue: 56.25,
        varianceValue: 25.00
      },
      // Liquids
      {
        sku: 'LIQ-OIL-001',
        name: 'Extra Virgin Olive Oil',
        type: 'liquids',
        category: 'Raw Materials',
        startStock: '15.5 liters',
        incoming: '50.0 liters',
        outgoing: '35.0 liters',
        adjustments: '0.0 liters',
        endStock: '30.5 liters',
        variance: '+15.0 liters',
        status: 'balanced',
        startValue: 135.63,
        incomingValue: 437.50,
        outgoingValue: 306.25,
        adjustmentsValue: 0.00,
        endValue: 266.88,
        varianceValue: 131.25
      },
      {
        sku: 'LIQ-MILK-001',
        name: 'Fresh Whole Milk',
        type: 'liquids',
        category: 'Raw Materials',
        startStock: '8.0 liters',
        incoming: '50.0 liters',
        outgoing: '45.0 liters',
        adjustments: '0.0 liters',
        endStock: '13.0 liters',
        variance: '+5.0 liters',
        status: 'balanced',
        startValue: 20.00,
        incomingValue: 125.00,
        outgoingValue: 112.50,
        adjustmentsValue: 0.00,
        endValue: 32.50,
        varianceValue: 12.50
      }
    ]
  },
  {
    id: 2,
    period: '2025-08',
    type: 'monthly',
    startDate: '2025-08-01',
    endDate: '2025-08-31',
    status: 'completed',
    totalStartValue: 12850.25,
    totalIncomingValue: 4100.50,
    totalOutgoingValue: 3530.25,
    totalAdjustmentsValue: -200.00,
    totalEndValue: 13220.50,
    totalVariance: 370.25,
    products: []
  },
  {
    id: 3,
    period: '2025-W37',
    type: 'weekly',
    startDate: '2025-09-08',
    endDate: '2025-09-14',
    status: 'in_progress',
    totalStartValue: 15931.00,
    totalIncomingValue: 750.25,
    totalOutgoingValue: 1200.50,
    totalAdjustmentsValue: 50.00,
    totalEndValue: 15630.75,
    totalVariance: -300.25,
    products: []
  }
])

const periodOptions = [
  { value: 'monthly', label: 'Monthly' },
  { value: 'weekly', label: 'Weekly' },
  { value: 'daily', label: 'Daily' },
  { value: 'custom', label: 'Custom Range' }
]

// Computed properties
const closeoutSummary = computed(() => {
  const total = closeoutPeriods.value.length
  const completed = closeoutPeriods.value.filter(c => c.status === 'completed').length
  const inProgress = closeoutPeriods.value.filter(c => c.status === 'in_progress').length
  const completionRate = total > 0 ? (completed / total) * 100 : 0
  
  return {
    totalCloseouts: total,
    completedCloseouts: completed,
    inProgressCloseouts: inProgress,
    completionRate: completionRate
  }
})

const filteredCloseouts = computed(() => {
  if (selectedPeriod.value === 'custom') {
    return closeoutPeriods.value.filter(closeout => {
      if (selectedDateRange.value.start && selectedDateRange.value.end) {
        const closeoutDate = new Date(closeout.startDate)
        return closeoutDate >= new Date(selectedDateRange.value.start) && 
               closeoutDate <= new Date(selectedDateRange.value.end)
      }
      return true
    })
  }
  return closeoutPeriods.value.filter(closeout => closeout.type === selectedPeriod.value)
})

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

const getProductTypeColor = (type) => {
  switch (type) {
    case 'items':
      return 'blue'
    case 'bulk':
      return 'green'
    case 'liquids':
      return 'orange'
    default:
      return 'gray'
  }
}

// Methods
const createNewCloseout = () => {
  const currentDate = new Date()
  const period = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`
  
  const newCloseout = {
    id: Date.now(),
    period: period,
    type: 'monthly',
    startDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).toISOString().split('T')[0],
    endDate: new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).toISOString().split('T')[0],
    status: 'in_progress',
    totalStartValue: 0,
    totalIncomingValue: 0,
    totalOutgoingValue: 0,
    totalAdjustmentsValue: 0,
    totalEndValue: 0,
    totalVariance: 0,
    products: []
  }
  
  closeoutPeriods.value.unshift(newCloseout)
}

const viewCloseoutDetails = (closeout) => {
  selectedCloseout.value = closeout
  showCloseoutDetails.value = true
}

const handleCompleteCloseout = (id) => {
  if (confirmAction('Are you sure you want to complete this closeout?')) {
    const closeout = closeoutPeriods.value.find(c => c.id === id)
    if (closeout) {
      closeout.status = 'completed'
    }
  }
}

onMounted(() => {
  store.loadSettings()
})
</script>

<template>
  <div class="closeout" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Inventory Closeout 
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Unified closeout reports for all product types: discrete items, bulk materials, and liquids
      </p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Total Closeouts"
        :value="closeoutSummary.totalCloseouts"
        subtitle="All Periods"
        icon="fas fa-chart-bar"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Completed"
        :value="closeoutSummary.completedCloseouts"
        subtitle="Finished"
        icon="fas fa-check-circle"
        color="green"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="In Progress"
        :value="closeoutSummary.inProgressCloseouts"
        subtitle="Active"
        icon="fas fa-clock"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Completion Rate"
        :value="`${closeoutSummary.completionRate.toFixed(1)}%`"
        subtitle="Success Rate"
        icon="fas fa-percentage"
        color="purple"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Period Selection -->
    <div :class="[darkModeClasses.card, 'p-4 rounded-lg shadow-sm border mb-6']">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <InventoryDropdown
          v-model="selectedPeriod"
          :options="periodOptions"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <div v-if="selectedPeriod === 'custom'" class="grid grid-cols-2 gap-2">
          <InventoryInput
            v-model="selectedDateRange.start"
            type="date"
            label="Start Date"
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />
          <InventoryInput
            v-model="selectedDateRange.end"
            type="date"
            label="End Date"
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />
        </div>

        <InventoryButton
          @click="createNewCloseout()"
          variant="primary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-plus mr-2"></i>
          New Closeout
        </InventoryButton>
      </div>
    </div>

    <!-- Closeout List -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border overflow-hidden']">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Period</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Start Value</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Incoming</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Outgoing</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Adjustments</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">End Value</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Variance</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="closeout in filteredCloseouts" :key="closeout.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                {{ closeout.period }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ closeout.type }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(closeout.totalStartValue) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(closeout.totalIncomingValue) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(closeout.totalOutgoingValue) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(closeout.totalAdjustmentsValue) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(closeout.totalEndValue) }}
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                <span :class="closeout.totalVariance >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ formatCurrency(closeout.totalVariance) }}
                </span>
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <CleanBadge 
                  :variant="closeout.status === 'completed' ? 'green' : closeout.status === 'in_progress' ? 'orange' : 'blue'" 
                  :text="closeout.status.charAt(0).toUpperCase() + closeout.status.slice(1).replace('_', ' ')" 
                  size="xs" 
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton variant="view" :iconStyle="scalingStyles.iconSize" @click="viewCloseoutDetails(closeout)" />
                  <ActionButton variant="success" :iconStyle="scalingStyles.iconSize" @click="handleCompleteCloseout(closeout.id)" :disabled="closeout.status === 'completed'" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Closeout Details Modal -->
    <div v-if="showCloseoutDetails" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-6xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            Closeout Details: {{ selectedCloseout?.period }}
          </h3>
          <button @click="showCloseoutDetails = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div v-if="selectedCloseout" class="space-y-4">
          <!-- Closeout Summary -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium mb-2">Start Value</h4>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">{{ formatCurrency(selectedCloseout.totalStartValue) }}</p>
            </div>
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium mb-2">Incoming</h4>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-2xl font-bold text-green-600">{{ formatCurrency(selectedCloseout.totalIncomingValue) }}</p>
            </div>
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium mb-2">Outgoing</h4>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-2xl font-bold text-red-600">{{ formatCurrency(selectedCloseout.totalOutgoingValue) }}</p>
            </div>
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium mb-2">End Value</h4>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">{{ formatCurrency(selectedCloseout.totalEndValue) }}</p>
            </div>
          </div>

          <!-- Product Type Summary -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-box text-blue-500"></i>
                <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium">Discrete Items</h4>
              </div>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-xl font-bold">
                {{ formatCurrency(selectedCloseout.products.filter(p => p.type === 'items').reduce((sum, p) => sum + p.endValue, 0)) }}
              </p>
            </div>
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-weight-hanging text-green-500"></i>
                <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium">Bulk Materials</h4>
              </div>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-xl font-bold">
                {{ formatCurrency(selectedCloseout.products.filter(p => p.type === 'bulk').reduce((sum, p) => sum + p.endValue, 0)) }}
              </p>
            </div>
            <div :class="[darkModeClasses.card, 'p-4 rounded-lg border']">
              <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-tint text-orange-500"></i>
                <h4 :style="scalingStyles.label" :class="darkModeClasses.text" class="text-sm font-medium">Liquids</h4>
              </div>
              <p :style="scalingStyles.amountFontSize" :class="darkModeClasses.text" class="text-xl font-bold">
                {{ formatCurrency(selectedCloseout.products.filter(p => p.type === 'liquids').reduce((sum, p) => sum + p.endValue, 0)) }}
              </p>
            </div>
          </div>

          <!-- Product Details Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead :class="darkModeClasses.tableHeader">
                <tr>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">SKU</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Product</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Start</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">In</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Out</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Adj</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">End</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Variance</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
                <tr v-for="item in selectedCloseout.products" :key="item.sku" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
                  <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap text-sm">
                    <CleanBadge 
                      :variant="getProductTypeColor(item.type)" 
                      :text="item.type" 
                      size="xs" 
                    />
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    {{ item.sku }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.name }}
                    <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
                      {{ item.category }}
                    </div>
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.startStock }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm text-green-600">
                    {{ item.incoming }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm text-red-600">
                    {{ item.outgoing }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.adjustments }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.endStock }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    <span :class="item.variance.startsWith('+') ? 'text-green-600' : 'text-red-600'">
                      {{ item.variance }}
                    </span>
                  </td>
                  <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                    <CleanBadge 
                      :variant="item.status === 'balanced' ? 'green' : 'orange'" 
                      :text="item.status === 'balanced' ? 'Balanced' : 'Variance'" 
                      size="xs" 
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>