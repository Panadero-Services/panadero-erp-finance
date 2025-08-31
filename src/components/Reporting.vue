<script setup>
import { ref, onMounted } from 'vue';
import { useReporting } from '../composables/useReporting';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import { useScaling } from '../../../shared/composables/useScaling.js'

const { fontSizes, scalingStyles, spacing } = useScaling()

// Remove all computed styles - use directly

// Filters state
const filters = ref({
  reportType: '',
  period: ''
});

// Dropdown options
const reportTypeOptions = [
  { label: 'All Reports', value: '' },
  { label: 'Financial', value: 'financial' },
  { label: 'Tax', value: 'tax' },
  { label: 'Budget', value: 'budget' },
  { label: 'Compliance', value: 'compliance' }
];

const periodOptions = [
  { label: 'All Periods', value: '' },
  { label: 'Current Month', value: 'current' },
  { label: 'Previous Month', value: 'previous' },
  { label: 'Quarter', value: 'quarter' },
  { label: 'Year', value: 'year' }
];

// Mock data for demonstration
const totalRevenue = ref(250000);
const totalExpenses = ref(180000);
const netIncome = ref(70000);
const totalAssets = ref(500000);
const totalLiabilities = ref(200000);
const equity = ref(300000);
const filteredReports = ref([
  { id: 1, name: 'Income Statement', type: 'financial', period: '2025-01', status: 'generated', lastRun: '2025-01-15' },
  { id: 2, name: 'Balance Sheet', type: 'financial', period: '2025-01', status: 'generated', lastRun: '2025-01-15' }
]);

// Missing functions
function handleNewReport() {
  // TODO: Implement new report functionality
  console.log('New report clicked');
}

function exportReports() {
  // TODO: Implement export functionality
  console.log('Export reports clicked');
}

function refreshData() {
  // TODO: Implement refresh functionality
  console.log('Refresh data clicked');
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

// Additional missing functions
function viewReport(report) {
  // TODO: Implement view functionality
  console.log('View report:', report);
}

function downloadReport(report) {
  // TODO: Implement download functionality
  console.log('Download report:', report);
}

function deleteReport(reportId) {
  if (confirm('Are you sure you want to delete this report?')) {
    // TODO: Implement delete functionality
    console.log('Delete report:', reportId);
    alert(`Deleted report with ID: ${reportId}`);
  }
}
</script>
<template>
  <div class="reporting dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Financial Reporting</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <FinanceDropdown
            v-model="filters.reportType"
            :options="reportTypeOptions"
            option-label="label"
            option-value="value"
            placeholder="All Report Types"
            variant="ghost"
            size="normal"
          />
          <FinanceDropdown
            v-model="filters.period"
            :options="periodOptions"
            option-label="label"
            option-value="value"
            placeholder="All Periods"
            variant="ghost"
            size="normal"
          />
        </div>
        <!-- Buttons -->
        <div :style="scalingStyles.buttonGap" class="flex items-center">
          <FinanceButton
            @click="handleNewReport"
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Report
          </FinanceButton>
          <FinanceButton
            @click="exportReports"
            variant="success"
            size="normal"
            icon-left="fas fa-download"
          >
            Export
          </FinanceButton>
          <FinanceButton
            @click="refreshData"
            variant="info"
            size="normal"
            icon-left="fas fa-refresh"
          >
            Refresh
          </FinanceButton>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <FinanceValueCard title="Total Revenue" :value="totalRevenue" rows="2-row" format="currency" color="positive" icon="fas fa-arrow-up" trend="up" />

      <FinanceValueCard title="Total Expenses" :value="totalExpenses" rows="2-row" format="currency" color="negative" icon="fas fa-arrow-down" trend="down" />

      <FinanceValueCard title="Net Income" :value="netIncome" rows="2-row" format="currency" color="auto" :min-good="50000" :min-warning="20000" icon="fas fa-chart-line" />
    </div>

    <!-- Financial Position -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <FinanceValueCard 
        title="Total Assets"
        :value="totalAssets"
        rows="2-row"
        format="currency"
        color="neutral"
        icon="fas fa-building"
      />

      <FinanceValueCard 
        title="Total Liabilities"
        :value="totalLiabilities"
        rows="2-row"
        format="currency"
        color="auto"
        :max-good="150000"
        :max-warning="300000"
        icon="fas fa-file-invoice"
      />

      <FinanceValueCard 
        title="Equity"
        :value="equity"
        rows="2-row"
        format="currency"
        color="positive"
        icon="fas fa-chart-pie"
      />
        </div>

    <!-- Reports Table -->
    <div :style="scalingStyles.sectionMargin" class="bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700">
      <div :style="scalingStyles.cardPadding" class="border-b dark:border-gray-700">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-700 dark:text-gray-300">Generated Reports</h3>
      </div>
      <div class="overflow-x-auto">
        <table :style="scalingStyles.borderRadius" class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Report Name</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Type</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Period</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Status</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Last Run</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in filteredReports" :key="report.id" class="dark:text-gray-100 dark:border-gray-600" :style="scalingStyles.tableRowHeight">
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ report.name }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ report.type }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ report.period }}</td>
              <td :style="[scalingStyles.paddingScale]" class="border dark:border-gray-600">
                <StatusBadge :status="report.status" />
              </td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(report.lastRun) }}</td>
              <td :style="scalingStyles.paddingScale" class="border dark:border-gray-600">
                <div class="flex gap-2">
                  <button 
                    @click="viewReport(report)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    title="View Report"
                  >
                    <i class="fas fa-eye" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="downloadReport(report)" 
                    class="p-2 rounded-md hover:bg-green-50 dark:hover:bg-green-900/20 text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors"
                    title="Download Report"
                  >
                    <i class="fas fa-download" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="deleteReport(report.id)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    title="Delete Report"
                  >
                    <i class="fas fa-trash" :style="scalingStyles.iconSize"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* CashFlow-style summary cards */
.cf-summary {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.summary-card {
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
  @apply dark:bg-gray-800 dark:shadow-gray-900/50;
}

.summary-card h3 {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 0.5rem;
  @apply dark:text-gray-300;
}

.summary-card p {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  @apply dark:text-white;
}

.cf-totals {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.total-card {
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
  @apply dark:bg-gray-800 dark:shadow-gray-900/50;
}

.total-card h4 {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 0.5rem;
  @apply dark:text-gray-300;
}

.total-card p {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0;
}

/* Color classes */
.positive {
  color: #059669;
  @apply dark:text-green-400;
}

.negative {
  color: #dc2626;
  @apply dark:text-red-400;
}

.amount {
  color: #111827;
  @apply dark:text-white;
}
</style>