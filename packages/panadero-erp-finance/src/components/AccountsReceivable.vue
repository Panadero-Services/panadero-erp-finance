
<script setup>
import { ref, onMounted } from 'vue';
import { useAccountsReceivable } from '../composables/useAccountsReceivable';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import { useFinanceStore } from '../stores/financeStore';
import { useScaling } from '../../../shared/composables/useScaling.js'
import StatusBadge from './ui/StatusBadge.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';

const store = useFinanceStore();
const { fontSizes, scalingStyles, spacing } = useScaling()

// Remove all computed styles - use store directly

// Filters state
const filters = ref({
  customer: '',
  status: ''
});

// Dropdown options for FinanceDropdown
const statusOptions = [
  { label: 'All Statuses', value: '' },
  { label: 'Draft', value: 'draft' },
  { label: 'Pending', value: 'pending' },
  { label: 'Approved', value: 'approved' },
  { label: 'Paid', value: 'paid' }
];

const categoryOptions = [
  { label: 'Revenue', value: 'revenue' },
  { label: 'Service', value: 'service' },
  { label: 'Product', value: 'product' },
  { label: 'Other', value: 'other' }
];

// Mock data for demonstration
const totalReceivables = ref(25000);
const overdueAmount = ref(5000);
const dueThisWeek = ref(8000);
const receivedThisMonth = ref(12000);
const filteredReceivables = ref([
  { id: 1, invoice_no: 'AR-001', customer_name: 'Customer 1', invoice_date: '2025-01-15', due_date: '2025-02-15', amount: 1500, status: 'pending' },
  { id: 2, invoice_no: 'AR-002', customer_name: 'Customer 2', invoice_date: '2025-01-20', due_date: '2025-02-20', amount: 3000, status: 'approved' }
]);

function getStatusBadgeClass(status) {
  const classes = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    approved: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400'
  };
  return classes[status] || classes.draft;
}

// Missing functions
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
  selectedCustomer,
  dateRange,
  receivables,
  summary,
  createReceivable,
  recordPayment,
  calculateDaysOverdue,
  init
} = useAccountsReceivable();

const {
  generateInvoiceNumber,
  createInvoice,
  getInvoiceStats,
  exportInvoicesToCSV,
  fetchInvoices,
  invoices: apiInvoices,
  isLoading: invoiceLoading,
  error: invoiceError
} = useInvoiceApi();

// UI state
const showNewReceivableForm = ref(false);
const showPaymentForm = ref(false);
const showBulkActions = ref(false);
const selectedReceivable = ref(null);
const selectedReceivables = ref([]);

// Form models
const newReceivable = ref({
  customer_id: '',
  invoice_no: '',
  invoice_date: '',
  due_date: '',
  amount: 0,
  notes: '',
  description: '',
  category: 'revenue'
});

const payment = ref({
  date: new Date().toISOString().split('T')[0],
  amount: 0,
  reference_no: '',
  notes: ''
});

// Mock customers data (replace with actual data from your backend)
const customers = ref([
  { id: 1, name: 'Customer 1' },
  { id: 2, name: 'Customer 2' }
]);

function isOverdue(receivable) {
  return calculateDaysOverdue(receivable.due_date) > 0;
}

// Clear all filters
function clearFilters() {
  selectedCustomer.value = null;
  dateRange.value = {
    start: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
    end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().split('T')[0]
  };
}

// View receivable details
function viewReceivableDetails(receivable) {
  // TODO: Implement view details modal
  console.log('View details for:', receivable);
  alert(`Viewing details for ${receivable.invoice_no}`);
}

// Edit receivable
function editReceivable(receivable) {
  // TODO: Implement edit modal
  console.log('Edit receivable:', receivable);
  alert(`Editing ${receivable.invoice_no}`);
}

// Delete receivable
function deleteReceivable(receivable) {
  if (confirm(`Are you sure you want to delete ${receivable.invoice_no}?`)) {
    // TODO: Implement delete functionality
    console.log('Delete receivable:', receivable);
    alert(`Deleted ${receivable.invoice_no}`);
  }
}

// Bulk actions
function bulkRecordPayment() {
  if (selectedReceivables.value.length === 0) return;
  // TODO: Implement bulk payment recording
  console.log('Bulk record payment for:', selectedReceivables.value);
  alert(`Recording payment for ${selectedReceivables.value.length} receivables`);
  showBulkActions.value = false;
}

function bulkExport() {
  if (selectedReceivables.value.length === 0) return;
  // TODO: Implement bulk export
  console.log('Bulk export for:', selectedReceivables.value);
  alert(`Exporting ${selectedReceivables.value.length} receivables`);
  showBulkActions.value = false;
}

function bulkDelete() {
  if (selectedReceivables.value.length === 0) return;
  if (confirm(`Are you sure you want to delete ${selectedReceivables.value.length} receivables?`)) {
    // TODO: Implement bulk delete
    console.log('Bulk delete for:', selectedReceivables.value);
    alert(`Deleted ${selectedReceivables.value.length} receivables`);
    selectedReceivables.value = [];
    showBulkActions.value = false;
  }
}

async function handleNewReceivable() {
  try {
    // Auto-generate invoice number if not provided
    if (!newReceivable.value.invoice_no) {
      newReceivable.value.invoice_no = generateInvoiceNumber('AR');
    }
    
    // Create invoice using the invoice system
    const invoice = await createInvoice('AR', {
      customer_id: newReceivable.value.customer_id,
      invoice_no: newReceivable.value.invoice_no,
      invoice_date: newReceivable.value.invoice_date,
      due_date: newReceivable.value.due_date,
      amount: newReceivable.value.amount,
      notes: newReceivable.value.notes,
      description: newReceivable.value.description || 'Customer Invoice',
      category: newReceivable.value.category
    });
    
    console.log('Created invoice:', invoice);
    
  showNewReceivableForm.value = false;
  newReceivable.value = {
    customer_id: '',
    invoice_no: '',
    invoice_date: '',
    due_date: '',
    amount: 0,
      notes: '',
      description: '',
      category: 'revenue'
  };
  } catch (error) {
    console.error('Error creating receivable:', error);
    alert('Error creating receivable: ' + error.message);
  }
}

function openPaymentForm(receivable) {
  selectedReceivable.value = receivable;
  payment.value.amount = receivable.amount - receivable.received_amount;
  showPaymentForm.value = true;
}

function closePaymentForm() {
  showPaymentForm.value = false;
  selectedReceivable.value = null;
  payment.value = {
    date: new Date().toISOString().split('T')[0],
    amount: 0,
    reference_no: '',
    notes: ''
  };
}

async function handlePayment() {
  await recordPayment(selectedReceivable.value.id, payment.value);
  closePaymentForm();
}

async function exportReceivables() {
  exportInvoicesToCSV('AR');
}

onMounted(async () => {
  await init();
  // Fetch invoices for this section
  await fetchInvoices({ section: 'AR' });
});
</script>

<template>
  <div class="accounts-receivable dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Accounts Receivable</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          

<FinanceInput v-model="value" size="normal" type="text" />

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
            @click="handleNewReceivable" 
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Receivable
          </FinanceButton>
          <FinanceButton 
            @click="exportReceivables" 
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
      <FinanceValueCard title="Total Receivables" :value="summary.total" rows="2-row" format="currency" color="neutral" icon="fas fa-receipt" />

      <FinanceValueCard title="Received Amount" :value="summary.received" rows="2-row" format="currency" color="positive" icon="fas fa-check-circle" />

      <FinanceValueCard title="Outstanding" :value="summary.outstanding" rows="2-row" format="currency" color="auto" :max-good="5000" :max-warning="15000" icon="fas fa-clock" />

      <FinanceValueCard title="Overdue" :value="summary.aging.older + summary.aging['90days'] + summary.aging['60days']" rows="2-row" format="currency" color="auto" :max-good="1000" :max-warning="5000" icon="fas fa-exclamation-triangle" />
    </div>

    <!-- Aging Summary -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4">
      <FinanceValueCard title="Current" :value="summary.aging.current" rows="2-row" format="currency" color="positive" size="small" />

      <FinanceValueCard title="1-30 Days" :value="summary.aging['30days']" rows="2-row" format="currency" color="warning" size="small" />

      <FinanceValueCard title="31-60 Days" :value="summary.aging['60days']" rows="2-row" format="currency" color="warning" size="small" />

      <FinanceValueCard title="61-90 Days" :value="summary.aging['90days']" rows="2-row" format="currency" color="warning" size="small" />

      <FinanceValueCard title=">90 Days" :value="summary.aging.older" rows="2-row" format="currency" color="negative" size="small" />
    </div>

    <!-- Receivables Table -->
    <div class="bg-white dark:bg-gray-800 rounded border dark:border-gray-700 mb-6">
      <div class="p-4 border-b bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold dark:text-white">Receivables</h3>
      </div>

      <div v-if="receivables.length > 0" class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700 text-left">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Invoice #</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Customer</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Date</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Due Date</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Amount</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Received</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Outstanding</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Status</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="receivable in receivables" :key="receivable.id" 
                :class="{ 'bg-red-50 dark:bg-red-900/20': isOverdue(receivable) }" 
                class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-gray-100"
                :style="scalingStyles.tableRowHeight">
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ receivable.invoice_no }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ receivable.customer_name }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(receivable.invoice_date) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(receivable.due_date) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(receivable.amount) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(receivable.received_amount) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(receivable.amount - receivable.received_amount) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">
                <StatusBadge :status="receivable.status" />
              </td>
              <td class="p-3 border dark:border-gray-600">
                <div class="flex flex-wrap gap-2">
                  <button 
                    @click="openPaymentForm(receivable)" 
                    :disabled="receivable.status === 'paid'"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-md transition-colors"
                    :title="'Record payment for ' + receivable.invoice_no"
                  >
                    <i class="fas fa-dollar-sign" :style="scalingStyles.iconSizeSmall"></i>
                  Record Payment
                </button>
                  <button 
                    @click="viewReceivableDetails(receivable)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    :title="'View details for ' + receivable.invoice_no"
                  >
                    <i class="fas fa-eye" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="editReceivable(receivable)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    :title="'Edit ' + receivable.invoice_no"
                  >
                    <i class="fas fa-edit" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="deleteReceivable(receivable)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    :title="'Delete ' + receivable.invoice_no"
                  >
                    <i class="fas fa-trash" :style="scalingStyles.iconSize"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
        No receivables found for the selected criteria
      </div>
    </div>

    <!-- New Receivable Form Modal -->
    <div v-if="showNewReceivableForm">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showNewReceivableForm = false"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <div class="flex-1 overflow-y-auto">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold mb-4 dark:text-white">New Receivable</h3>
        <form @submit.prevent="handleNewReceivable">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Customer</label>
              <FinanceDropdown 
                v-model="newReceivable.customer_id" 
                :options="[{ label: 'Select Customer', value: '' }, ...customers.map(c => ({ label: c.name, value: c.id }))]"
                option-label="label"
                option-value="value"
                placeholder="Select Customer"
                variant="outline"
                size="normal"
                full-width
              />
            </div>

            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Invoice Number</label>
              <input v-model="newReceivable.invoice_no" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>

            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Invoice Date</label>
              <input type="date" v-model="newReceivable.invoice_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Due Date</label>
              <input type="date" v-model="newReceivable.due_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Amount</label>
              <input type="number" v-model="newReceivable.amount" step="0.01" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Category</label>
              <FinanceDropdown 
                v-model="newReceivable.category" 
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
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
            <input v-model="newReceivable.description" placeholder="Invoice description" class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

          <div class="mb-4">
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Notes</label>
            <textarea v-model="newReceivable.notes" class="w-full border rounded p-2 h-20 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <FinanceButton type="button" @click="showNewReceivableForm = false" variant="outline-secondary" size="normal">
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
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold mb-4 dark:text-white">Record Payment</h3>
        <form @submit.prevent="handlePayment">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Payment Date</label>
              <input type="date" v-model="payment.date" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
          </div>

            <div>
              <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Amount</label>
            <input type="number" 
                   v-model="payment.amount" 
                   :max="selectedReceivable.amount - selectedReceivable.received_amount"
                   step="0.01" 
                     required 
                     class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>
          </div>

          <div class="mb-4">
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 mb-2">Reference Number</label>
            <input v-model="payment.reference_no" required class="w-full border rounded p-2" />
          </div>

          <div class="mb-4">
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 mb-2">Notes</label>
            <textarea v-model="payment.notes" class="w-full border rounded p-2 h-20"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <FinanceButton type="button" @click="closePaymentForm" variant="outline-secondary" size="normal">
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

    <!-- Bulk Actions Modal -->
    <div v-if="showBulkActions">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showBulkActions = false"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <div class="flex-1 overflow-y-auto">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold mb-4 dark:text-white">Bulk Actions</h3>
        <div class="space-y-4">
          <div>
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Select Receivables</label>
            <div class="max-h-40 overflow-y-auto border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
              <div v-for="receivable in receivables" :key="receivable.id" class="flex items-center gap-2 py-1">
                <input type="checkbox" 
                       :value="receivable.id" 
                       v-model="selectedReceivables" 
                       class="rounded" />
                <span :style="scalingStyles.smallFontSize" class="dark:text-gray-100">{{ receivable.invoice_no }} - {{ receivable.customer_name }} ({{ formatCurrency(receivable.amount) }})</span>
              </div>
            </div>
          </div>
          
          <div class="flex flex-wrap gap-2">
            <button @click="bulkRecordPayment" 
                    :disabled="selectedReceivables.length === 0"
                    :style="scalingStyles.buttonPadding"
                    class="bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
              Record Payment
            </button>
            <button @click="bulkExport" 
                    :disabled="selectedReceivables.length === 0"
                    :style="scalingStyles.buttonPadding"
                    class="bg-green-600 text-white rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
              Export Selected
            </button>
            <button @click="bulkDelete" 
                    :disabled="selectedReceivables.length === 0"
                    :style="scalingStyles.buttonPadding"
                    class="bg-red-600 text-white rounded hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600">
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


<!-- No custom styles needed - using Tailwind CSS classes -->
