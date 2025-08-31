

<script setup>
import { ref, onMounted } from 'vue';
import { useCashFlow } from '../composables/useCashFlow';
import { useInvoiceApi } from '../composables/useInvoiceApi';
//import { useFinanceStore } from '../stores/financeStore';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';

//const store = useFinanceStore();
import { useScaling } from '../../../shared/composables/useScaling.js'
const { fontSizes, scalingStyles, spacing } = useScaling()

// Remove all computed styles - use store directly

const {
  isLoading,
  selectedPeriod,
  selectedCategory,
  transactions,
  summary,
  monthlyFlow,
  addTransaction,
  updateTransaction,
  deleteTransaction: deleteTransactionFromComposable,
  init
} = useCashFlow();

const {
  generateInvoiceNumber,
  createInvoice,
  exportInvoicesToCSV,
  fetchInvoices,
  invoices: apiInvoices,
  isLoading: invoiceLoading,
  error: invoiceError
} = useInvoiceApi();

// UI state
const showNewTransactionForm = ref(false);
const showEditForm = ref(false);
const editingTransaction = ref(null);

// Filters state
const filters = ref({
  period: '',
  category: ''
});

// Form models
const newTransaction = ref({
  category_id: '',
  transaction_date: new Date().toISOString().split('T')[0],
  type: 'inflow',
  amount: 0,
  description: '',
  reference_no: ''
});

// Mock categories data (replace with actual data from your backend)
const categories = ref([
  { id: 1, name: 'Sales Revenue', type: 'operating' },
  { id: 2, name: 'Operating Expenses', type: 'operating' },
  { id: 3, name: 'Equipment Purchase', type: 'investing' },
  { id: 4, name: 'Loan Proceeds', type: 'financing' },
  { id: 5, name: 'Loan Repayment', type: 'financing' }
]);

// Dropdown options
const categoryOptions = ref([
  { label: 'All Categories', value: '' },
  ...categories.value.map(cat => ({ label: cat.name, value: cat.id }))
]);

const typeOptions = ref([
  { label: 'Inflow', value: 'inflow' },
  { label: 'Outflow', value: 'outflow' }
]);

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

async function exportCashFlow() {
  try {
    // Export current transactions to CSV
    const csvContent = exportInvoicesToCSV(transactions.value, 'cash-flow');
    console.log('Cash Flow exported:', csvContent);
  } catch (error) {
    console.error('Export failed:', error);
  }
}

function editTransaction(transaction) {
  editingTransaction.value = { ...transaction };
  showEditForm.value = true;
}

async function handleDeleteTransaction(transactionId) {
  try {
    // Delete from cash flow
    await deleteTransactionFromComposable(transactionId);
    console.log('Transaction deleted:', transactionId);
  } catch (error) {
    console.error('Delete failed:', error);
  }
}

async function handleNewTransaction() {
  // Auto-generate reference number if not provided
  if (!newTransaction.value.reference_no) {
    newTransaction.value.reference_no = generateInvoiceNumber('CF');
  }
  
      // Create invoice using the invoice system
    const invoice = await createInvoice('CF', {
    category_id: newTransaction.value.category_id,
    transaction_date: newTransaction.value.transaction_date,
    type: newTransaction.value.type,
    amount: newTransaction.value.amount,
    description: newTransaction.value.description,
    reference_no: newTransaction.value.reference_no
  });
  
  // Also add to cash flow for backward compatibility
  await addTransaction(newTransaction.value);
  
  showNewTransactionForm.value = false;
  newTransaction.value = {
    category_id: '',
    transaction_date: new Date().toISOString().split('T')[0],
    type: 'inflow',
    amount: 0,
    description: '',
    reference_no: ''
  };
}

function closeEditForm() {
  showEditForm.value = false;
  editingTransaction.value = null;
}

async function handleEditTransaction() {
  await updateTransaction(editingTransaction.value.id, editingTransaction.value);
  closeEditForm();
}

onMounted(async () => {
  await init();
  // Fetch invoices for this section
  await fetchInvoices({ section: 'CF' });
});
</script>
<template>
  <div class="cash-flow dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Cash Flow Management</h2>
      <div class="flex items-center gap-2">
      </div>
      <div :style="scalingStyles.gapScale" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <input 
            v-model="filters.period" 
            type="text" 
            placeholder="Period (YYYY-MM)" 
            :style="[scalingStyles.inputPadding,, scalingStyles.textFontSize]"
            class="border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-blue-400"
          >
          <FinanceDropdown
            v-model="filters.category"
            :options="categoryOptions"
            placeholder="All Categories"
            variant="outline"
          />
        </div>
        <!-- Buttons -->
        <div :style="[scalingStyles.buttonPadding, scalingStyles.gapScale]" class="flex items-center">
          <FinanceButton
            @click="showNewTransactionForm = true"
            variant="primary"
            icon-left="fas fa-plus"
          >
            New Transaction
          </FinanceButton>
          <FinanceButton
            @click="exportCashFlow"
            variant="success"
            icon-left="fas fa-download"
          >
            Export
          </FinanceButton>
        </div>
      </div>
    </div>

    <div :style="spacing.sectionMargin" class="cf-summary">
      <FinanceValueCard title="Operating Activities" :value="summary.operating" rows="2-row" format="currency" color="auto" :min-good="0" :min-warning="-10000" icon="fas fa-cogs" />
      <FinanceValueCard title="Investing Activities" :value="summary.investing" rows="2-row" format="currency" color="auto" :min-good="0" :min-warning="-50000" icon="fas fa-chart-line" />
      <FinanceValueCard title="Financing Activities" :value="summary.financing" rows="2-row" format="currency" color="auto" :min-good="0" :min-warning="-25000" icon="fas fa-university" />
      <FinanceValueCard title="Net Cash Flow" :value="summary.netCashFlow" rows="2-row" format="currency" color="auto" :min-good="1000" :min-warning="-1000" icon="fas fa-money-bill-wave" />
    </div>

    <div :style="spacing.sectionMargin" class="cf-totals">
      <FinanceValueCard title="Total Inflow" :value="summary.totalInflow" rows="2-row" format="currency" color="positive" icon="fas fa-arrow-up" trend="up" />
      <FinanceValueCard title="Total Outflow" :value="summary.totalOutflow" rows="2-row" format="currency" color="negative" icon="fas fa-arrow-down" trend="down" />
    </div>

    <div class="cf-content">
      <div v-if="transactions.length > 0" class="transactions-list">
        <table :style="scalingStyles.borderRadius" class="w-full border-collapse bg-white dark:bg-gray-800 rounded border dark:border-gray-700">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700" :style="scalingStyles.tableHeaderHeight">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Date</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Category</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Description</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Type</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Amount</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Reference</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="transaction in transactions" 
              :key="transaction.id" 
              class="dark:text-gray-100 dark:border-gray-600"
              :style="scalingStyles.tableRowHeight"
            >
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(transaction.transaction_date) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ transaction.category_name }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ transaction.description }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">
                <StatusBadge :status="transaction.type" />
              </td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(transaction.amount) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ transaction.reference_no || '-' }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">
                <div class="flex items-center gap-1">
                  <button 
                    @click="editTransaction(transaction)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    title="Edit Transaction"
                  >
                    <i class="fas fa-edit" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="handleDeleteTransaction(transaction.id)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    title="Delete Transaction"
                  >
                    <i class="fas fa-trash" :style="scalingStyles.iconSize"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else :style="scalingStyles.titleFontSizee" class="no-transactions text-gray-500 dark:text-gray-400 p-6 text-center italic">
        No transactions found for the selected criteria
      </div>
    </div>

    <!-- New Transaction Form Modal -->
    <div v-if="showNewTransactionForm">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showNewTransactionForm = false"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <div class="flex-1 overflow-y-auto">
            <h3 :style="scalingStyles.titleFontSizeSize" class="font-semibold mb-4 dark:text-white">New Cash Flow Transaction</h3>
            <form @submit.prevent="handleNewTransaction">
              <div class="grid grid-cols-2 gap-4 mb-4">
                                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Category</label>
                    <FinanceDropdown
                      v-model="newTransaction.category_id"
                      :options="categories"
                      option-label="name"
                      option-value="id"
                      placeholder="Select Category"
                      variant="filled"
                      full-width
                    />
                  </div>

                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Transaction Date</label>
                    <input type="date" v-model="newTransaction.transaction_date" required :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                  </div>

                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Type</label>
                    <FinanceDropdown
                      v-model="newTransaction.type"
                      :options="typeOptions"
                      variant="filled"
                      full-width
                    />
                  </div>

                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Amount</label>
                    <input type="number" v-model="newTransaction.amount" step="0.01" required :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                  </div>
              </div>

              <div class="mb-4">
                <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
                <textarea v-model="newTransaction.description" required :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding, scalingStyles.textareaHeight]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
              </div>

              <div class="mb-4">
                <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Reference Number</label>
                <input v-model="newTransaction.reference_no" :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
              </div>

              <div class="flex justify-end gap-2">
                <FinanceButton
                  type="button"
                  @click="showNewTransactionForm = false"
                  variant="outline-secondary"
                >
                  Cancel
                </FinanceButton>
                <FinanceButton
                  type="submit"
                  :disabled="isLoading"
                  variant="primary"
                  icon-left="fas fa-save"
                >
                  Save
                </FinanceButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Transaction Form Modal -->
    <div v-if="showEditForm">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="closeEditForm"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <div class="flex-1 overflow-y-auto">
            <h3 :style="scalingStyles.titleFontSizeSize" class="font-semibold mb-4 dark:text-white">Edit Transaction</h3>
            <form @submit.prevent="handleEditTransaction">
              <div class="grid grid-cols-2 gap-4 mb-4">
                                  <div>
                   <label :style="scalingStyles.titleFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Category</label>
                    <FinanceDropdown
                      v-model="editingTransaction.category_id"
                      :options="categories"
                      option-label="name"
                      option-value="id"
                      variant="filled"
                      full-width
                    />
                  </div>

                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Transaction Date</label>
                    <input type="date" v-model="editingTransaction.transaction_date" required :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                  </div>

                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Type</label>
                    <FinanceDropdown
                      v-model="editingTransaction.type"
                      :options="typeOptions"
                      variant="filled"
                      full-width
                    />
                  </div>

                  <div>
                    <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Amount</label>
                    <input type="number" v-model="editingTransaction.amount" step="0.01" required :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                  </div>
              </div>

              <div class="mb-4">
                <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
                <textarea v-model="editingTransaction.description" required :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding, scalingStyles.textareaHeight]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
              </div>

              <div class="mb-4">
                <label :style="scalingStyles.titleFontSizee" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Reference Number</label>
                <input v-model="editingTransaction.reference_no" :style="[scalingStyles.titleFontSize, scalingStyles.inputPadding]" class="w-full border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
              </div>

              <div class="flex justify-end gap-2">
                <FinanceButton
                  type="button"
                  @click="closeEditForm"
                  variant="outline-secondary"
                >
                  Cancel
                </FinanceButton>
                <FinanceButton
                  type="submit"
                  :disabled="isLoading"
                  variant="primary"
                  icon-left="fas fa-save"
                >
                  Update
                </FinanceButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Standardized styling now controlled via useStyling.js composable */
/* Users can customize font sizes, colors, spacing, etc. from the centralized styling system */

.cash-flow {
  padding: 1rem;
}

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
  @apply dark:bg-gray-800 dark:shadow-gray-900/50;
}

.cf-totals {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.total-card {
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  @apply dark:bg-gray-800 dark:shadow-gray-900/50;
}

.positive {
  color: #059669;
  font-weight: bold;
  @apply dark:text-green-400;
}

.negative {
  color: #dc2626;
  font-weight: bold;
  @apply dark:text-red-400;
}

.inflow {
  color: #059669;
  font-weight: bold;
  @apply dark:text-green-400;
}

.outflow {
  color: #dc2626;
  font-weight: bold;
  @apply dark:text-red-400;
}



table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 0.5rem;
  border: 1px solid #ddd;
  text-align: left;
  @apply dark:border-gray-600;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 0.25rem;
  border: none;
  border-radius: 0.375rem;
  background: transparent;
  transition: all 0.2s ease;
  cursor: pointer;
}

.action-btn:hover {
  background: rgba(0, 0, 0, 0.05);
  transform: translateY(-1px);
}

.action-btn:active {
  transform: translateY(0);
}

.edit-btn:hover {
  background: rgba(59, 130, 246, 0.1);
}

.delete-btn:hover {
  background: rgba(220, 38, 38, 0.1);
}

/* Dark mode support */
.dark .action-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.dark .edit-btn:hover {
  background: rgba(59, 130, 246, 0.2);
}

.dark .delete-btn:hover {
  background: rgba(220, 38, 38, 0.2);
}


</style>
