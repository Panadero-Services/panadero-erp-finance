<script setup>
import { ref, onMounted } from 'vue';
import { useTaxManagement } from '../composables/useTaxManagement';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import { useFinanceStore } from '../stores/financeStore';
import { useScaling } from '../../../shared/composables/useScaling.js';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';

const store = useFinanceStore();
const { fontSizes, scalingStyles, spacing } = useScaling();

// Remove all computed styles - use store directly

const {
  isLoading,
  selectedPeriod,
  selectedTaxType,
  taxRecords,
  summary,
  availablePeriods,
  createTaxRecord,
  markTaxRecordAsFiled,
  markTaxRecordAsPaid,
  init
} = useTaxManagement();

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
const showNewTaxRecordForm = ref(false);
const showFilingForm = ref(false);
const showPaymentForm = ref(false);
const selectedRecord = ref(null);

// Dropdown options
const periodOptions = [
  { label: 'All Periods', value: '' },
  { label: '2025 Q1', value: '2025-Q1' },
  { label: '2024 Q4', value: '2024-Q4' },
  { label: '2024 Q3', value: '2024-Q3' },
  { label: '2024 Q2', value: '2024-Q2' },
  { label: '2024 Q1', value: '2024-Q1' }
];

const taxTypeOptions = [
  { label: 'All Types', value: '' },
  { label: 'Income Tax', value: 'income' },
  { label: 'Sales Tax', value: 'sales' },
  { label: 'VAT', value: 'vat' },
  { label: 'Payroll Tax', value: 'payroll' },
  { label: 'Property Tax', value: 'property' }
];

// Form models
const newTaxRecord = ref({
  tax_type: '',
  tax_period: '',
  taxable_amount: 0,
  tax_amount: 0,
  filing_due_date: '',
  payment_due_date: '',
  notes: ''
});

const filingDetails = ref({
  filing_date: new Date().toISOString().split('T')[0],
  return_reference: '',
  filing_method: '',
  notes: ''
});

const paymentDetails = ref({
  payment_date: new Date().toISOString().split('T')[0],
  payment_amount: 0,
  payment_method: '',
  reference_number: '',
  notes: ''
});

// Filters state
const filters = ref({
  taxType: '',
  status: ''
});

// Mock data for demonstration
const totalTaxAmount = ref(15000);
const pendingAmount = ref(8000);
const filedAmount = ref(5000);
const paidAmount = ref(2000);
const filteredTaxRecords = ref([
  { id: 1, tax_type: 'VAT', tax_period: '2025-01', taxable_amount: 10000, tax_amount: 2000, status: 'pending' },
  { id: 2, tax_type: 'income', tax_period: '2025-01', taxable_amount: 50000, tax_amount: 10000, status: 'filed' }
]);

function isOverdue(record) {
  const today = new Date();
  const dueDate = new Date(record.payment_due_date);
  return today > dueDate && record.status !== 'paid';
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

function getStatusBadgeClass(status) {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    filed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400'
  };
  return classes[status] || classes.pending;
}

async function handleNewTaxRecord() {
  try {
    // Create invoice using the invoice system
    const invoice = await createInvoice('TX', {
      description: `${newTaxRecord.value.tax_type} Tax for ${newTaxRecord.value.tax_period}`,
      amount: newTaxRecord.value.tax_amount,
      metadata: {
        tax_type: newTaxRecord.value.tax_type,
        tax_period: newTaxRecord.value.tax_period,
        taxable_amount: newTaxRecord.value.taxable_amount,
        filing_due_date: newTaxRecord.value.filing_due_date,
        payment_due_date: newTaxRecord.value.payment_due_date,
        notes: newTaxRecord.value.notes
      }
    });
    
    // Also create tax record for backward compatibility
    await createTaxRecord(newTaxRecord.value);
    
    showNewTaxRecordForm.value = false;
    newTaxRecord.value = {
      tax_type: '',
      tax_period: '',
      taxable_amount: 0,
      tax_amount: 0,
      filing_due_date: '',
      payment_due_date: '',
      notes: ''
    };
    
    console.log('Created tax invoice:', invoice);
  } catch (error) {
    console.error('Error creating tax record:', error);
    alert('Error creating tax record: ' + error.message);
  }
}

function openFilingForm(record) {
  selectedRecord.value = record;
  showFilingForm.value = true;
}

function closeFilingForm() {
  showFilingForm.value = false;
  selectedRecord.value = null;
  filingDetails.value = {
    filing_date: new Date().toISOString().split('T')[0],
    return_reference: '',
    filing_method: '',
    notes: ''
  };
}

async function handleFiling() {
  await markTaxRecordAsFiled(selectedRecord.value.id, filingDetails.value);
  closeFilingForm();
}

function openPaymentForm(record) {
  selectedRecord.value = record;
  paymentDetails.value.payment_amount = record.tax_amount;
  showPaymentForm.value = true;
}

function closePaymentForm() {
  showPaymentForm.value = false;
  selectedRecord.value = null;
  paymentDetails.value = {
    payment_date: new Date().toISOString().split('T')[0],
    payment_amount: 0,
    payment_method: '',
    reference_number: '',
    notes: ''
  };
}

async function handlePayment() {
  await markTaxRecordAsPaid(selectedRecord.value.id, paymentDetails.value);
  closePaymentForm();
}

async function exportTaxRecords() {
  exportInvoicesToCSV('TX');
}

function clearFilters() {
  filters.value = { taxType: '', status: '' };
}

onMounted(async () => {
  await init();
  // Fetch invoices for this section
  await fetchInvoices({ section: 'TX' });
});
</script>

<template>
  <div class="tax-management dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="fontSizes.titleFontSize" class="font-semibold dark:text-white">Tax Management</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <FinanceDropdown
            v-model="filters.taxType"
            :options="taxTypeOptions"
            option-label="label"
            option-value="value"
            placeholder="All Tax Types"
            variant="ghost"
            size="normal"
          />
          <FinanceDropdown
            v-model="filters.status"
            :options="[
              { label: 'All Statuses', value: '' },
              { label: 'Pending', value: 'pending' },
              { label: 'Filed', value: 'filed' },
              { label: 'Paid', value: 'paid' }
            ]"
            option-label="label"
            option-value="value"
            placeholder="All Statuses"
            variant="ghost"
            size="normal"
          />
        </div>
        <!-- Buttons -->
        <div :style="scalingStyles.buttonGap" class="flex items-center">
          <FinanceButton
            @click="handleNewTaxRecord"
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Tax Record
          </FinanceButton>
          <FinanceButton
            @click="exportTaxRecords"
            variant="success"
            size="normal"
            icon-left="fas fa-download"
          >
            Export
          </FinanceButton>
          <FinanceButton
            @click="clearFilters"
            variant="secondary"
            size="normal"
            icon-left="fas fa-times"
          >
            Clear Filters
          </FinanceButton>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <FinanceValueCard title="Total Tax Amount" :value="totalTaxAmount" rows="2-row" format="currency" color="neutral" icon="fas fa-receipt" />

      <FinanceValueCard title="Pending Amount" :value="pendingAmount" rows="2-row" format="currency" color="auto" :max-good="5000" :max-warning="10000" icon="fas fa-clock" />

      <FinanceValueCard title="Filed Amount" :value="filedAmount" rows="2-row" format="currency" color="positive" icon="fas fa-check-circle" />

      <FinanceValueCard title="Overdue Amount" :value="overdueAmount" rows="2-row" format="currency" color="auto" :max-good="1000" :max-warning="5000" icon="fas fa-exclamation-triangle" />
    </div>

    <!-- Tax Records Table -->
    <div class="bg-white dark:bg-gray-800 rounded border dark:border-gray-700 mb-6">
      <div class="p-4 border-b bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
        <h3 :style="fontSizes.subtitleFontSize" class="font-semibold dark:text-white">Tax Records</h3>
      </div>
      
      <div v-if="taxRecords.length > 0" class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700 text-left">
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Tax Type</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Period</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Taxable Amount</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Tax Amount</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Filing Due</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Payment Due</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Status</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in taxRecords" :key="record.id" 
                :class="{ 'bg-red-50 dark:bg-red-900/20': isOverdue(record) }" 
                class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-gray-100"
                :style="scalingStyles.tableRowHeight">
              <td class="p-3 border dark:border-gray-600">{{ record.tax_type }}</td>
              <td class="p-3 border dark:border-gray-600">{{ record.tax_period }}</td>
              <td class="p-3 border dark:border-gray-600">{{ formatCurrency(record.taxable_amount) }}</td>
              <td class="p-3 border dark:border-gray-600">{{ formatCurrency(record.tax_amount) }}</td>
              <td class="p-3 border dark:border-gray-600">{{ formatDate(record.filing_due_date) }}</td>
              <td class="p-3 border dark:border-gray-600">{{ formatDate(record.payment_due_date) }}</td>
              <td class="p-3 border dark:border-gray-600">
                <StatusBadge :status="record.status" />
              </td>
              <td class="p-3 border dark:border-gray-600">
                <div class="flex flex-wrap gap-2">
                  <button 
                    v-if="record.status === 'pending'"
                    @click="openFilingForm(record)"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                  >
                    <i class="fas fa-file-upload" :style="scalingStyles.iconSizeSmall"></i>
                    File Return
                  </button>
                  <button 
                    v-if="record.status === 'filed'" 
                    @click="openPaymentForm(record)"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors"
                  >
                    <i class="fas fa-dollar-sign" :style="scalingStyles.iconSizeSmall"></i>
                    Record Payment
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="p-6 text-center text-gray-500 italic">
        No tax records found for the selected criteria
      </div>
    </div>

    <!-- New Tax Record Form Modal -->
    <div v-if="showNewTaxRecordForm">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showNewTaxRecordForm = false"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <!-- Content area -->
          <div class="flex-1 overflow-y-auto">
        <h3 :style="fontSizes.subtitleFontSize" class="font-semibold mb-4 dark:text-white">New Tax Record</h3>
        <form @submit.prevent="handleNewTaxRecord">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Tax Type</label>
              <select v-model="newTaxRecord.tax_type" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600">
                <option value="">Select Tax Type</option>
                <option value="VAT">VAT</option>
                <option value="income">Income Tax</option>
                <option value="payroll">Payroll Tax</option>
                <option value="property">Property Tax</option>
              </select>
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Tax Period</label>
              <input type="month" v-model="newTaxRecord.tax_period" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Taxable Amount</label>
              <input type="number" v-model="newTaxRecord.taxable_amount" step="0.01" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Tax Amount</label>
              <input type="number" v-model="newTaxRecord.tax_amount" step="0.01" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Filing Due Date</label>
              <input type="date" v-model="newTaxRecord.filing_due_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>

            <div>
              <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Payment Due Date</label>
              <input type="date" v-model="newTaxRecord.payment_due_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
            </div>
          </div>

          <div class="mb-4">
            <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Notes</label>
            <textarea v-model="newTaxRecord.notes" class="w-full border rounded p-2 h-20 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <FinanceButton type="button" @click="showNewTaxRecordForm = false" variant="outline-secondary" size="normal">Cancel</FinanceButton>
            <FinanceButton type="submit" :disabled="isLoading" variant="primary" size="normal">Save</FinanceButton>
          </div>
        </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Filing Form Modal -->
    <div v-if="showFilingForm">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="closeFilingForm"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <div class="flex-1 overflow-y-auto">
            <h3 :style="fontSizes.subtitleFontSize" class="font-semibold mb-4 dark:text-white">File Tax Return</h3>
            <form @submit.prevent="handleFiling">
              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Filing Date</label>
                  <input type="date" v-model="filingDetails.filing_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                </div>

                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Return Reference</label>
                  <input v-model="filingDetails.return_reference" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                </div>

                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Filing Method</label>
                  <select v-model="filingDetails.filing_method" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600">
                    <option value="">Select Method</option>
                    <option value="online">Online</option>
                    <option value="mail">Mail</option>
                    <option value="fax">Fax</option>
                  </select>
                </div>
              </div>

              <div class="mb-4">
                <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Notes</label>
                <textarea v-model="filingDetails.notes" class="w-full border rounded p-2 h-20 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
              </div>

              <div class="flex justify-end gap-2">
                <FinanceButton type="button" @click="closeFilingForm" variant="outline-secondary" size="normal">Cancel</FinanceButton>
                <FinanceButton type="submit" :disabled="isLoading" variant="primary" size="normal">File Return</FinanceButton>
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
            <h3 :style="fontSizes.subtitleFontSize" class="font-semibold mb-4 dark:text-white">Record Tax Payment</h3>
            <form @submit.prevent="handlePayment">
              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Payment Date</label>
                  <input type="date" v-model="paymentDetails.payment_date" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                </div>

                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Payment Amount</label>
                  <input type="number" 
                         v-model="paymentDetails.payment_amount" 
                         :max="selectedRecord.tax_amount"
                         step="0.01" 
                         required 
                         class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                </div>

                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Payment Method</label>
                  <select v-model="paymentDetails.payment_method" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600">
                    <option value="">Select Method</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="cash">Cash</option>
                  </select>
                </div>

                <div>
                  <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Reference Number</label>
                  <input v-model="paymentDetails.reference_number" required class="w-full border rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" />
                </div>
              </div>

              <div class="mb-4">
                <label :style="fontSizes.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Notes</label>
                <textarea v-model="paymentDetails.notes" class="w-full border rounded p-2 h-20 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"></textarea>
              </div>

              <div class="flex justify-end gap-2">
                <FinanceButton type="button" @click="closePaymentForm" variant="outline-secondary" size="normal">Cancel</FinanceButton>
                <FinanceButton type="submit" :disabled="isLoading" variant="primary" size="normal">Record Payment</FinanceButton>
              </div>
            </form>
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
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.warning {
  color: #d97706;
  @apply dark:text-yellow-400;
}

.amount {
  color: #111827;
  @apply dark:text-white;
}
</style>