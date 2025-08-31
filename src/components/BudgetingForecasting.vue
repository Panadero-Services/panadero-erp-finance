<script setup>
import { ref, onMounted } from 'vue';
import { useBudgeting } from '../composables/useBudgeting';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import { useScaling } from '../../../shared/composables/useScaling.js'

const { fontSizes, scalingStyles, spacing } = useScaling()

// Filters state
const filters = ref({
  period: '',
  department: ''
});

// Dropdown options
const periodOptions = [
  { label: 'All Periods', value: '' },
  { label: '2025', value: '2025' },
  { label: '2024', value: '2024' },
  { label: 'Q1 2025', value: '2025-Q1' },
  { label: 'Q2 2025', value: '2025-Q2' },
  { label: 'Q3 2025', value: '2025-Q3' },
  { label: 'Q4 2025', value: '2025-Q4' }
];

const departmentOptions = [
  { label: 'All Departments', value: '' },
  { label: 'Operations', value: 'operations' },
  { label: 'Marketing', value: 'marketing' },
  { label: 'Sales', value: 'sales' },
  { label: 'Finance', value: 'finance' },
  { label: 'HR', value: 'hr' },
  { label: 'IT', value: 'it' }
];

// Mock data for demonstration
const totalBudget = ref(500000);
const actualSpending = ref(320000);
const variance = ref(180000);
const variancePercentage = ref(36);
const filteredBudgets = ref([
  { id: 1, department: 'Operations', period: '2025', budget_amount: 200000, actual_amount: 150000, variance: 50000, status: 'under' },
  { id: 2, department: 'Marketing', period: '2025', budget_amount: 100000, actual_amount: 80000, variance: 20000, status: 'under' }
]);

function getVarianceClass(variance) {
  return variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
}

// Missing functions
function handleNewBudget() {
  // TODO: Implement new budget functionality
  console.log('New budget clicked');
}

function exportBudgets() {
  // TODO: Implement export functionality
  console.log('Export budgets clicked');
}

function refreshData() {
  // TODO: Implement refresh functionality
  console.log('Refresh data clicked');
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

// Additional missing functions
function editBudget(budget) {
  // TODO: Implement edit functionality
  console.log('Edit budget:', budget);
}

function deleteBudget(budgetId) {
  if (confirm('Are you sure you want to delete this budget?')) {
    // TODO: Implement delete functionality
    console.log('Delete budget:', budgetId);
    alert(`Deleted budget with ID: ${budgetId}`);
  }
}
</script>

<template>
  <div class="budgeting-forecasting dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Budgeting & Forecasting</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <FinanceDropdown
            v-model="filters.period"
            :options="periodOptions"
            option-label="label"
            option-value="value"
            placeholder="All Periods"
            variant="ghost"
            size="normal"
          />
          <FinanceDropdown
            v-model="filters.department"
            :options="departmentOptions"
            option-label="label"
            option-value="value"
            placeholder="All Departments"
            variant="ghost"
            size="normal"
          />
        </div>
        <!-- Buttons -->
        <div :style="scalingStyles.buttonGap" class="flex items-center">
          <FinanceButton
            @click="handleNewBudget"
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Budget
          </FinanceButton>
          <FinanceButton
            @click="exportBudgets"
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
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <FinanceValueCard title="Total Budget" :value="totalBudget" rows="2-row" format="currency" color="neutral" icon="fas fa-chart-pie" />

      <FinanceValueCard title="Actual Spending" :value="actualSpending" rows="2-row" format="currency" color="info" icon="fas fa-money-bill-wave" />

      <FinanceValueCard title="Variance" :value="variance" rows="2-row" format="currency" color="auto" :min-good="100000" :min-warning="50000" icon="fas fa-balance-scale" />

      <FinanceValueCard title="Variance %" :value="variancePercentage" rows="2-row" format="percentage" color="auto" :min-good="20" :min-warning="10" icon="fas fa-percentage" />
    </div>

    <!-- Budgets Table -->
    <div :style="scalingStyles.sectionMargin" class="bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700">
      <div :style="scalingStyles.cardPadding" class="border-b dark:border-gray-700">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-700 dark:text-gray-300">Budgets List</h3>
      </div>
      <div class="overflow-x-auto">
        <table :style="scalingStyles.borderRadius" class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Department</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Period</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Budget Amount</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actual Amount</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Variance</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Status</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
            <tr v-for="budget in filteredBudgets" :key="budget.id" class="dark:text-gray-100 dark:border-gray-600" :style="scalingStyles.tableRowHeight">
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ budget.department }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ budget.period }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(budget.budget_amount) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(budget.actual_amount) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600" :class="getVarianceClass(budget.variance)">
                {{ formatCurrency(budget.variance) }}
              </td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">
                <StatusBadge :status="budget.status" />
              </td>
              <td :style="scalingStyles.paddingScale" class="border dark:border-gray-600">
                <div class="flex gap-2">
                  <button 
                    @click="editBudget(budget)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    title="Edit Budget"
                  >
                    <i class="fas fa-edit" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="deleteBudget(budget.id)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    title="Delete Budget"
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