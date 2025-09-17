<!--
  Closeout Component
  @version 1.0.12
  @date 16-Sep-2025
  @description Monthly and weekly inventory closeout reports
-->
<template>
  <div class="closeout" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Inventory Closeout
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Monthly and weekly inventory closeout reports with start, incoming, outgoing, adjustments, and end stock
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
                <StatusBadge 
                  :status="closeout.status === 'completed' ? 'success' : closeout.status === 'in_progress' ? 'warning' : 'info'"
                  :text="closeout.status.charAt(0).toUpperCase() + closeout.status.slice(1)"
                  :scaling-styles="scalingStyles"
                  :dark-mode="store.settings.darkMode"
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton  variant="view" @click="viewCloseoutDetails(closeout)" />
                  <ActionButton  variant="success" @click="handleCompleteCloseout(closeout.id)" :disabled="closeout.status === 'completed'" />
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

          <!-- Product Details Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead :class="darkModeClasses.tableHeader">
                <tr>
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
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    {{ item.sku }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ item.name }}
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
                    <span :class="item.variance >= 0 ? 'text-green-600' : 'text-red-600'">
                      {{ item.variance }}
                    </span>
                  </td>
                  <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                    <StatusBadge 
                      :status="item.status === 'balanced' ? 'success' : 'warning'"
                      :text="item.status === 'balanced' ? 'Balanced' : 'Variance'"
                      :scaling-styles="scalingStyles"
                      :dark-mode="store.settings.darkMode"
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

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCloseout } from '../composables/useCloseout.js'
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
  formatDate,
  confirmAction 
} = useCommonSnippets()
const { 
  closeoutPeriods,
  selectedPeriod,
  selectedDateRange,
  getCloseoutsByPeriod,
  getCloseoutsByStatus,
  getCurrentPeriodCloseout,
  getCloseoutSummary,
  createCloseout,
  updateCloseout,
  completeCloseout 
} = useCloseout()

// State
const showCloseoutDetails = ref(false)
const selectedCloseout = ref(null)

const periodOptions = [
  { value: 'monthly', label: 'Monthly' },
  { value: 'weekly', label: 'Weekly' },
  { value: 'daily', label: 'Daily' },
  { value: 'custom', label: 'Custom Range' }
]

// Computed properties
const closeoutSummary = computed(() => getCloseoutSummary.value)

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
  return getCloseoutsByPeriod.value(selectedPeriod.value)
})

// Dark mode classes are now provided by useCommonSnippets

// Methods
const createNewCloseout = () => {
  const currentDate = new Date()
  const period = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`
  
  createCloseout({
    period: period,
    type: 'monthly',
    startDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).toISOString().split('T')[0],
    endDate: new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).toISOString().split('T')[0]
  })
}

const viewCloseoutDetails = (closeout) => {
  selectedCloseout.value = closeout
  showCloseoutDetails.value = true
}

const handleCompleteCloseout = (id) => {
  if (confirm('Are you sure you want to complete this closeout?')) {
    completeCloseout(id)
  }
}

// formatCurrency is now provided by useCommonSnippets

onMounted(() => {
  store.loadSettings()
})
</script>
