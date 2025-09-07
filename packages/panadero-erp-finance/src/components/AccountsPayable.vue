<script setup>
import { ref, onMounted } from 'vue';
import { useAccountsPayable } from '../composables/useAccountsPayable';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import { useFinanceStore } from '../stores/financeStore';
import { useScaling } from '../../../shared/composables/useScaling.js';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';

const store = useFinanceStore();
const { fontSizes, scalingStyles, spacing } = useScaling();

// Remove all computed styles - use store directly

// Filters state
const filters = ref({
  vendor: '',
  status: ''
});

// Dropdown options for FinanceDropdown
const statusOptions = [
  { label: 'All Statuses', value: '' },
  { label: 'Pending', value: 'pending' },
  { label: 'Approved', value: 'approved' },
  { label: 'Paid', value: 'paid' }
];

const categoryOptions = [
  { label: 'Expense', value: 'expense' },
  { label: 'Service', value: 'service' },
  { label: 'Inventory', value: 'inventory' },
  { label: 'Equipment', value: 'equipment' },
  { label: 'Other', value: 'other' }
];

// Mock data for demonstration
const totalPayables = ref(15000);
const overdueAmount = ref(2500);
const dueThisWeek = ref(5000);
const paidThisMonth = ref(8000);
const filteredPayables = ref([
  { id: 1, invoice_no: 'INV-001', vendor_name: 'Vendor 1', invoice_date: '2025-01-15', due_date: '2025-02-15', amount: 1000, status: 'pending' },
  { id: 2, invoice_no: 'INV-002', vendor_name: 'Vendor 2', invoice_date: '2025-01-20', due_date: '2025-02-20', amount: 2500, status: 'approved' }
]);

// Missing functions
function handleNewPayable() {
  // TODO: Implement new payable functionality
  console.log('New payable clicked');
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

const {
  isLoading,
  selectedVendor,
  dateRange,
  payables,
  summary,
  createPayable,
  recordPayment,
  calculateDaysOverdue,
  init
} = useAccountsPayable();

const {
  generateInvoiceNumber,
  createInvoice,
  getInvoiceStats,
  exportInvoicesToCSV,
  fetchInvoices,
  invoices: apiInvoices,
  isLoading: invoiceLoading,
  error: invoiceError,
  isAuthenticated,
  getAuthStatus
} = useInvoiceApi();

// UI state
const showNewPayableForm = ref(false);
const showPaymentForm = ref(false);
const showBulkActions = ref(false);
const selectedPayable = ref(null);
const selectedPayables = ref([]);

// Form models
const newPayable = ref({
  vendor_id: '',
  invoice_no: '',
  invoice_date: '',
  due_date: '',
  amount: 0,
  notes: '',
  description: '',
  category: 'expense'
});

const payment = ref({
  date: new Date().toISOString().split('T')[0],
  amount: 0,
  reference_no: '',
  notes: ''
});

// Mock vendors data (replace with actual data from your backend)
const vendors = ref([
  { id: 1, name: 'Vendor 1' },
  { id: 2, name: 'Vendor 2' }
]);

function isOverdue(payable) {
  return calculateDaysOverdue(payable.due_date) > 0;
}

function openPaymentForm(payable) {
  selectedPayable.value = payable;
  payment.value.amount = payable.amount - payable.paid_amount;
  showPaymentForm.value = true;
}

function closePaymentForm() {
  showPaymentForm.value = false;
  selectedPayable.value = null;
  payment.value = {
    date: new Date().toISOString().split('T')[0],
    amount: 0,
    reference_no: '',
    notes: ''
  };
}

async function handlePayment() {
  await recordPayment(selectedPayable.value.id, payment.value);
  closePaymentForm();
}

async function exportPayables() {
  exportInvoicesToCSV('AP');
}

// Clear all filters
function clearFilters() {
  selectedVendor.value = null;
  dateRange.value = {
    start: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
    end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().split('T')[0]
  };
}

// View payable details
function viewPayableDetails(payable) {
  // TODO: Implement view details modal
  console.log('View details for:', payable);
  alert(`Viewing details for ${payable.invoice_no}`);
}

// Edit payable
function editPayable(payable) {
  // TODO: Implement edit modal
  console.log('Edit payable:', payable);
  alert(`Editing ${payable.invoice_no}`);
}

// Delete payable
function deletePayable(payableId) {
  if (confirm(`Are you sure you want to delete this payable?`)) {
    // TODO: Implement delete functionality
    console.log('Delete payable:', payableId);
    alert(`Deleted payable with ID: ${payableId}`);
  }
}

// Bulk actions
function bulkRecordPayment() {
  if (selectedPayables.value.length === 0) return;
  // TODO: Implement bulk payment recording
  console.log('Bulk record payment for:', selectedPayables.value);
  alert(`Recording payment for ${selectedPayables.value.length} payables`);
  showBulkActions.value = false;
}

function bulkExport() {
  if (selectedPayables.value.length === 0) return;
  // TODO: Implement bulk export
  console.log('Bulk export for:', selectedPayables.value);
  alert(`Exporting ${selectedPayables.value.length} payables`);
  showBulkActions.value = false;
}

function bulkDelete() {
  if (selectedPayables.value.length === 0) return;
  if (confirm(`Are you sure you want to delete ${selectedPayables.value.length} payables?`)) {
    // TODO: Implement bulk delete
    console.log('Bulk delete for:', selectedPayables.value);
    alert(`Deleted ${selectedPayables.value.length} payables`);
    selectedPayables.value = [];
    showBulkActions.value = false;
  }
}

onMounted(async () => {
  await init();
  // Fetch invoices for this section
  await fetchInvoices({ section: 'AP' });
});
</script>
<template>
  <div class="accounts-payable dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="fontSizes.titleFontSize" class="font-semibold dark:text-white">Accounts Payable</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <input 
            v-model="filters.vendor" 
            type="text" 
            placeholder="Vendor Name" 
            :style="[scalingStyles.inputPadding, fontSizes.textFontSize]"
            class="border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          >
          <FinanceDropdown 
            v-model="filters.status" 
            :options="statusOptions"
            option-label="label"
            option-value="value"
            placeholder="All Statuses"
            variant="ghost"
            size="normal"
          />
        </div>
        <!-- Buttons -->
        <div class="flex items-center gap-2">
          <FinanceButton 
            @click="handleNewPayable" 
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Payable
          </FinanceButton>
          <FinanceButton 
            @click="exportPayables" 
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
      
      <FinanceValueCard title="Total Payables" :value="totalPayables" rows="2-row" format="currency" color="neutral" icon="fas fa-file-invoice-dollar" />
      <FinanceValueCard title="Overdue Amount" :value="overdueAmount" rows="2-row" format="currency" color="auto" :max-good="1000" :max-warning="5000" icon="fas fa-exclamation-triangle" />
      <FinanceValueCard title="Due This Week" :value="dueThisWeek" rows="2-row" format="currency" color="auto" :max-good="3000" :max-warning="7000" icon="fas fa-calendar-week" />
      <FinanceValueCard title="Paid This Month" :value="paidThisMonth" rows="2-row" format="currency" color="positive" icon="fas fa-check-circle" />
    </div>

    <!-- Aging Summary -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4">
      <FinanceValueCard title="Current" :value="summary.aging.current" rows="2-row" format="currency" color="positive" size="small" />
      <FinanceValueCard title="1-30 Days" :value="summary.aging['30days']" rows="2-row" format="currency" color="warning" size="small" />
      <FinanceValueCard title="31-60 Days" :value="summary.aging['60days']" rows="2-row" format="currency" color="warning" size="small" />
      <FinanceValueCard title="61-90 Days" :value="summary.aging['90days']" rows="2-row" format="currency" color="warning" size="small" />
      <FinanceValueCard title=">90 Days" :value="summary.aging.older" rows="2-row" format="currency" color="negative" size="small" />
    </div>

    <!-- Error Display -->
    <div v-if="invoiceError" class="error-message bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 :style="fontSizes.smallFontSize" class="font-medium text-red-800 dark:text-red-200">Error</h3>
          <div :style="fontSizes.smallFontSize" class="mt-2 text-red-700 dark:text-red-300">
            <p>{{ invoiceError }}</p>
            <p v-if="invoiceError.includes('Authentication')" class="mt-1">
              <a href="/login" class="font-medium underline hover:text-red-600 dark:hover:text-red-400">Go to Login</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Payables Table -->
    <div :style="scalingStyles.sectionMargin" class="bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700">
      <div :style="scalingStyles.cardPadding" class="border-b dark:border-gray-700">
        <h3 :style="fontSizes.subtitleFontSize" class="font-semibold text-gray-700 dark:text-gray-300">Payables List</h3>
      </div>
      <div class="overflow-x-auto">
        <table :style="scalingStyles.borderRadius" class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Invoice #</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Vendor</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Date</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Due Date</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Amount</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Status</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="payable in filteredPayables" :key="payable.id" class="dark:text-gray-100 dark:border-gray-600" :style="scalingStyles.tableRowHeight">
              <td :style="[fontSizes.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ payable.invoice_no }}</td>
              <td :style="[fontSizes.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ payable.vendor_name }}</td>
              <td :style="[fontSizes.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(payable.invoice_date) }}</td>
              <td :style="[fontSizes.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(payable.due_date) }}</td>
              <td :style="[fontSizes.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(payable.amount) }}</td>
              <td :style="scalingStyles.paddingScale" class="border dark:border-gray-600">
                <StatusBadge :status="payable.status" />
              </td>
              <td :style="scalingStyles.paddingScale"  class="border dark:border-gray-600">
                <div class="flex gap-2">
                  <button 
                    @click="editPayable(payable)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    title="Edit Payable"
                  >
                    <i class="fas fa-edit" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="deletePayable(payable.id)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    title="Delete Payable"
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

    <!-- New Payable Form Modal -->
                <div v-if="showNewPayableForm">
                  <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showNewPayableForm = false"></div>
                  <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
                    <div class="h-full flex flex-col">
                      <div class="flex-1 overflow-y-auto">
        <h3 :style="fontSizes.subtitleFontSize" class="font-semibold mb-4">New Payable</h3>
        <form @submit.prevent="handleNewPayable">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Vendor</label>
              <FinanceDropdown 
                v-model="newPayable.vendor_id" 
                :options="[{ label: 'Select Vendor', value: '' }, ...vendors.map(v => ({ label: v.name, value: v.id }))]"
                option-label="label"
                option-value="value"
                placeholder="Select Vendor"
                variant="outline"
                size="normal"
                full-width
              />
          </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Invoice Number</label>
              <input v-model="newPayable.invoice_no" placeholder="Leave blank to auto-generate" class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
              <small :style="fontSizes.smallFontSize" class="text-gray-500 dark:text-gray-400">Leave blank to auto-generate</small>
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Invoice Date</label>
              <input type="date" v-model="newPayable.invoice_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Due Date</label>
              <input type="date" v-model="newPayable.due_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Amount</label>
              <input type="number" v-model="newPayable.amount" step="0.01" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Category</label>
              <FinanceDropdown 
                v-model="newPayable.category" 
                :options="categoryOptions"
                option-label="label"
                option-value="value"
                placeholder="Select Category"
                variant="outline"
                size="normal"
                full-width
              />
            </div>
          </div>

          <div class="mb-4">
            <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
            <input v-model="newPayable.description" placeholder="Invoice description" class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

          <div class="mb-4">
            <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Notes</label>
            <textarea v-model="newPayable.notes" class="w-full border rounded p-2 h-20 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
          </div>

                                <div class="flex justify-end gap-2">
                        <FinanceButton type="button" @click="showNewPayableForm = false" variant="outline-secondary" size="normal">
                          Cancel
                        </FinanceButton>
                        <FinanceButton type="submit" :disabled="isLoading" variant="primary" size="normal">
                          Save
                        </FinanceButton>
          </div>
        </form>
                      </div>
                    </div>
      </div>
    </div>

    <!-- Payment Form Modal -->
                <div v-if="showPaymentForm">
                  <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="closePaymentForm"></div>
                  <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
                    <div class="h-full flex flex-col">
                      <div class="flex-1 overflow-y-auto">
        <h3 :style="fontSizes.subtitleFontSize" class="font-semibold mb-4">Record Payment</h3>
        <form @submit.prevent="handlePayment">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Payment Date</label>
              <input type="date" v-model="payment.date" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Amount</label>
            <input type="number" 
                   v-model="payment.amount" 
                   :max="selectedPayable.amount - selectedPayable.paid_amount"
                   step="0.01" 
                     required 
                     class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>
          </div>

          <div class="mb-4">
            <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 mb-2">Reference Number</label>
            <input v-model="payment.reference_no" required class="w-full border rounded p-2" />
          </div>

          <div class="mb-4">
            <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 mb-2">Notes</label>
            <textarea v-model="payment.notes" class="w-full border rounded p-2 h-20"></textarea>
          </div>

                                <div class="flex justify-end gap-2">
                        <button type="button" @click="closePaymentForm" class="px-3 py-2 rounded bg-gray-100">Cancel</button>
                        <button type="submit" :disabled="isLoading" class="px-3 py-2 rounded bg-indigo-600 text-white disabled:opacity-50">Save</button>
          </div>
        </form>
                      </div>
                    </div>
                  </div>
                </div>

                    <!-- Bulk Actions Modal -->
                <div v-if="showBulkActions">
                  <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showBulkActions = false"></div>
                  <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
                    <div class="h-full flex flex-col">
                      <div class="flex-1 overflow-y-auto">
        <h3 :style="fontSizes.subtitleFontSize" class="font-semibold mb-4 dark:text-white">Bulk Actions</h3>
        <div class="space-y-4">
          <div>
            <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Select Payables</label>
            <div class="max-h-40 overflow-y-auto border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
              <div v-for="payable in payables" :key="payable.id" class="flex items-center gap-2 py-1">
                <input type="checkbox" 
                       :value="payable.id" 
                       v-model="selectedPayables" 
                       class="rounded" />
                <span :style="fontSizes.smallFontSize" class="dark:text-gray-100">{{ payable.invoice_no }} - {{ payable.vendor_name }} ({{ formatCurrency(payable.amount) }})</span>
              </div>
            </div>
          </div>
          
          <div class="flex flex-wrap gap-2">
            <button @click="bulkRecordPayment" 
                    :disabled="selectedPayables.length === 0"
                    class="px-3 py-2 rounded bg-blue-600 text-white disabled:opacity-50 hover:bg-blue-700">
              Record Payment
            </button>
            <button @click="bulkExport" 
                    :disabled="selectedPayables.length === 0"
                    class="px-3 py-2 rounded bg-green-600 text-white disabled:opacity-50 hover:bg-green-700">
              Export Selected
            </button>
            <button @click="bulkDelete" 
                    :disabled="selectedPayables.length === 0"
                    class="px-3 py-2 rounded bg-red-600 text-white disabled:opacity-50 hover:bg-red-700">
              Delete Selected
            </button>
          </div>
        </div>
        
                            <div class="flex justify-end gap-2 mt-6">
                      <button @click="showBulkActions = false" class="px-3 py-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Close</button>
                    </div>
                      </div>
                    </div>
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
  grid-template-columns: repeat(5, 1fr);
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

.warning {
  color: #d97706;
  @apply dark:text-yellow-400;
}

.critical {
  color: #991b1b;
  @apply dark:text-red-500;
}

.amount {
  color: #111827;
  @apply dark:text-white;
}
</style>
