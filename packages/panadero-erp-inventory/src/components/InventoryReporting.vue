<script setup>
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'
import { useScaling } from 'panadero-shared-styling'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import InventoryValueCard from './ui/InventoryValueCard.vue'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

// State
const isLoading = ref(false)
const selectedReportType = ref('stock_levels')
const dateRange = ref({
  start: '',
  end: ''
})
const generatedReports = ref([])

// Computed
const reportTypes = [
  { value: 'stock_levels', label: 'Stock Levels Report' },
  { value: 'movement_summary', label: 'Movement Summary' },
  { value: 'low_stock', label: 'Low Stock Alert' },
  { value: 'value_analysis', label: 'Value Analysis' },
  { value: 'supplier_performance', label: 'Supplier Performance' }
]

const reportData = computed(() => {
  switch (selectedReportType.value) {
    case 'stock_levels':
      return {
        title: 'Current Stock Levels',
        data: store.stockItems.map(item => ({
          sku: item.sku,
          name: item.name,
          currentStock: item.currentStock,
          minStock: item.minStock,
          maxStock: item.maxStock,
          status: item.currentStock <= item.minStock ? 'Low' : 'Normal'
        }))
      }
    case 'movement_summary':
      return {
        title: 'Stock Movement Summary',
        data: store.stockMovements.reduce((acc, movement) => {
          const key = movement.movementType
          acc[key] = (acc[key] || 0) + movement.quantity
          return acc
        }, {})
      }
    case 'low_stock':
      return {
        title: 'Low Stock Items',
        data: store.getLowStockItems
      }
    case 'value_analysis':
      return {
        title: 'Inventory Value Analysis',
        data: {
          totalValue: store.getTotalInventoryValue,
          averageValue: store.getTotalInventoryValue / store.stockItems.length,
          categoryBreakdown: store.stockItems.reduce((acc, item) => {
            acc[item.category] = (acc[item.category] || 0) + (item.currentStock * item.unitCost)
            return acc
          }, {})
        }
      }
    case 'supplier_performance':
      return {
        title: 'Supplier Performance',
        data: store.suppliers.map(supplier => ({
          name: supplier.name,
          rating: supplier.rating,
          status: supplier.status,
          paymentTerms: supplier.paymentTerms
        }))
      }
    default:
      return { title: 'No Data', data: [] }
  }
})

// Actions
const generateReport = async () => {
  isLoading.value = true
  
  try {
    const report = await store.generateReport(selectedReportType.value, {
      dateRange: dateRange.value,
      filters: {}
    })
    
    generatedReports.value.unshift({
      ...report,
      data: reportData.value.data,
      generatedAt: new Date().toISOString()
    })
  } catch (error) {
    console.error('Error generating report:', error)
  } finally {
    isLoading.value = false
  }
}

const exportReport = (report) => {
  // Simulate export functionality
  console.log('Exporting report:', report)
  alert('Report exported successfully!')
}

const printReport = (report) => {
  // Simulate print functionality
  window.print()
}

const deleteReport = (reportId) => {
  const index = generatedReports.value.findIndex(r => r.id === reportId)
  if (index !== -1) {
    generatedReports.value.splice(index, 1)
  }
}

onMounted(() => {
  // Set default date range to current month
  const now = new Date()
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0)
  
  dateRange.value = {
    start: firstDay.toISOString().split('T')[0],
    end: lastDay.toISOString().split('T')[0]
  }
})
</script>

<template>
  <div class="inventory-reporting">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="scalingStyles.titleFontSize" class="font-bold text-gray-900 dark:text-white mb-2">
        Inventory Reports
      </h1>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">
        Generate and analyze inventory reports
      </p>
    </div>

    <!-- Report Generator -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <h2 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
        Generate Report
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <InventoryDropdown
          v-model="selectedReportType"
          :options="reportTypes"
          label="Report Type"
          placeholder="Select report type"
        />
        
        <InventoryInput
          v-model="dateRange.start"
          label="Start Date"
          type="date"
        />
        
        <InventoryInput
          v-model="dateRange.end"
          label="End Date"
          type="date"
        />
      </div>
      
      <InventoryButton
        @click="generateReport"
        variant="primary"
        size="md"
        :loading="isLoading"
      >
        <i class="fas fa-chart-bar mr-2"></i>
        Generate Report
      </InventoryButton>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <InventoryValueCard
        title="Total Items"
        :value="store.stockItems.length"
        icon="fas fa-boxes"
        color="blue"
      />
      <InventoryValueCard
        title="Low Stock Items"
        :value="store.getLowStockItems.length"
        icon="fas fa-exclamation-triangle"
        color="red"
      />
      <InventoryValueCard
        title="Total Value"
        :value="`$${store.getTotalInventoryValue.toLocaleString()}`"
        icon="fas fa-dollar-sign"
        color="green"
      />
      <InventoryValueCard
        title="Active Suppliers"
        :value="store.suppliers.filter(s => s.status === 'active').length"
        icon="fas fa-truck"
        color="purple"
      />
    </div>

    <!-- Generated Reports -->
    <div v-if="generatedReports.length > 0">
        <h2 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
        Generated Reports
      </h2>
      
      <div class="space-y-4">
        <div
          v-for="report in generatedReports"
          :key="report.id"
          class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6"
        >
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white">{{ report.title }}</h3>
              <p :style="scalingStyles.smallFontSize" class="text-gray-600 dark:text-gray-400">
                Generated: {{ new Date(report.generatedAt).toLocaleString() }}
              </p>
            </div>
            <div class="flex space-x-2">
              <InventoryButton
                @click="exportReport(report)"
                variant="ghost"
                size="sm"
              >
                <i class="fas fa-download"></i>
              </InventoryButton>
              <InventoryButton
                @click="printReport(report)"
                variant="ghost"
                size="sm"
              >
                <i class="fas fa-print"></i>
              </InventoryButton>
              <InventoryButton
                @click="deleteReport(report.id)"
                variant="ghost"
                size="sm"
                class="text-red-600 hover:text-red-800"
              >
                <i class="fas fa-trash"></i>
              </InventoryButton>
            </div>
          </div>
          
          <!-- Report Data Display -->
          <div class="overflow-x-auto">
            <table v-if="Array.isArray(report.data)" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th
                    v-for="(value, key) in report.data[0] || {}"
                    :key="key"
                    :style="scalingStyles.smallFontSize"
                    class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ key }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="(item, index) in report.data" :key="index">
                  <td
                    v-for="(value, key) in item"
                    :key="key"
                    :style="scalingStyles.smallFontSize"
                    class="px-4 py-2 text-gray-900 dark:text-white"
                  >
                    {{ value }}
                  </td>
                </tr>
              </tbody>
            </table>
            
            <div v-else class="text-gray-600 dark:text-gray-400">
              <pre>{{ JSON.stringify(report.data, null, 2) }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- No Reports State -->
    <div v-else class="text-center py-12">
      <i :style="scalingStyles.iconSizeLarge" class="fas fa-chart-bar text-gray-400 mb-4"></i>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">No reports generated yet. Create your first report above.</p>
    </div>
  </div>
</template>

<style scoped>
.inventory-reporting {
  padding: 0;
}
</style>
