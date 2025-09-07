<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useGeneralLedger } from '../composables/useGeneralLedger';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import { useFinanceStore } from '../stores/financeStore';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import { useScaling } from '../../../shared/composables/useScaling.js'

const store = useFinanceStore();
const { fontSizes, scalingStyles, spacing } = useScaling()

// Remove all computed styles - use store directly

const {
  isLoading,
  currentPeriod,
  journalEntries,
  trialBalance,
  postJournalEntry,
  closePeriod
} = useGeneralLedger();

const {
  generateInvoiceNumber,
  createInvoice,
  exportInvoicesToCSV,
  fetchInvoices,
  invoices: apiInvoices,
  isLoading: invoiceLoading,
  error: invoiceError
} = useInvoiceApi();

const showNewEntryForm = ref(false);
const accounts = ref([
  'Cash',
  'Accounts Receivable',
  'Inventory',
  'Accounts Payable',
  'Revenue',
  'Expenses'
]);

// Dropdown options for FinanceDropdown
const entryTypeOptions = ref([
  { label: 'Debit', value: 'debit' },
  { label: 'Credit', value: 'credit' }
]);

const newEntry = ref({
  description: '',
  lines: [
    { account: '', type: 'debit', amount: 0 },
    { account: '', type: 'credit', amount: 0 }
  ]
});

// Reset form to initial state
function resetForm() {
  newEntry.value = {
    description: '',
    lines: [
      { account: '', type: 'debit', amount: 0 },
      { account: '', type: 'credit', amount: 0 }
    ]
  };
}

// Initialize form when opening
function openNewEntryForm() {
  showNewEntryForm.value = true;
  resetForm();
}

// Computed properties for balance validation
const totalDebits = ref(0);
const totalCredits = ref(0);

const balanceDifference = ref(0);
const isBalanced = ref(true);

const canSubmit = ref(false);

// Watch for changes in newEntry.lines to update totals
watch(() => newEntry.value.lines, () => {
  totalDebits.value = newEntry.value.lines
    .filter(line => line.type === 'debit')
    .reduce((sum, line) => sum + (parseFloat(line.amount) || 0), 0);
  totalCredits.value = newEntry.value.lines
    .filter(line => line.type === 'credit')
    .reduce((sum, line) => sum + (parseFloat(line.amount) || 0), 0);
  balanceDifference.value = Math.abs(totalDebits.value - totalCredits.value);
  isBalanced.value = Math.abs(totalDebits.value - totalCredits.value) < 0.01; // Allow for small rounding differences
  canSubmit.value = newEntry.value.description.trim() !== '' && 
         newEntry.value.lines.every(line => line.account && line.amount > 0);
}, { deep: true });

// Missing functions
function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

function addLine() {
  newEntry.value.lines.push({ account: '', type: 'debit', amount: 0 });
}

function removeLine(index) {
  newEntry.value.lines.splice(index, 1);
}

async function handleNewEntry() {
  try {
    // Create invoice using the invoice system
    const invoice = await createInvoice('GL', {
      description: newEntry.value.description,
      lines: newEntry.value.lines,
      period: currentPeriod.value,
      type: 'journal',
      category: 'adjustment'
    });
    
    // Also post to GL for backward compatibility
    await postJournalEntry(newEntry.value);
    
    showNewEntryForm.value = false;
    resetForm();
    alert('Journal entry posted successfully with invoice number: ' + invoice.invoice_number);
  } catch (error) {
    console.error('Post journal entry error:', error);
    alert(`Error posting journal entry: ${error.message}`);
  }
}

async function handleClosePeriod() {
  if (confirm('Are you sure you want to close the current period?')) {
    try {
      const nextPeriod = await closePeriod(currentPeriod.value);
      alert(`Period ${currentPeriod.value} closed successfully. New period: ${nextPeriod}`);
    } catch (error) {
      alert(`Error closing period: ${error.message}`);
      console.error('Close period error:', error);
    }
  }
}

// Filters state
const filters = ref({
  period: '',
  account: ''
});

const availablePeriods = computed(() => {
  return Object.keys(journalEntries.value).map(id => id.split('_')[0]);
});

const availableAccounts = computed(() => {
  const allAccounts = new Set();
  journalEntries.value.forEach(entry => {
    entry.lines.forEach(line => {
      if (line.account) {
        allAccounts.add(line.account);
      }
    });
  });
  return Array.from(allAccounts);
});

const filteredJournalEntries = computed(() => {
  return journalEntries.value.filter(entry => {
    const matchesPeriod = filters.value.period ? entry.period === filters.value.period : true;
    const matchesAccount = filters.value.account ? entry.lines.some(line => line.account === filters.value.account) : true;
    return matchesPeriod && matchesAccount;
  });
});

function exportGL() {
  exportInvoicesToCSV('GL');
}
</script>
<template>
  <div class="general-ledger dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">General Ledger</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <FinanceDropdown v-model="filters.period" :options="['', ...availablePeriods]" placeholder="All Periods" variant="ghost" size="normal" />
          <FinanceDropdown v-model="filters.account" :options="['', ...availableAccounts]" placeholder="All Accounts" variant="ghost" size="normal" />
        </div>

        <!-- Buttons -->
        <div :style="scalingStyles.buttonGap" class="flex items-center">
          <FinanceButton variant="primary" @click="handleNewEntry">New Entry</FinanceButton>
          <FinanceButton variant="success" @click="exportGL">Export GL</FinanceButton>
          <FinanceButton variant="secondary" @click="handleClosePeriod">Close Period</FinanceButton>
        </div>

      </div>
    </div>

    <div :style="scalingStyles.smallFontSize" class="mb-4 text-gray-600 dark:text-gray-400">
      Current Period: {{ currentPeriod }}
    </div>

    <!-- Summary Cards -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <FinanceValueCard title="Journal Entries" :value="journalEntries.length" rows="2-row" format="number" color="info" icon="fas fa-list" />
      <FinanceValueCard title="Active Accounts" :value="Object.keys(trialBalance).length" rows="2-row" format="number" color="info" icon="fas fa-chart-pie" />
      <FinanceValueCard title="Total Debits" :value="totalDebits" rows="2-row" format="currency" color="positive" icon="fas fa-plus-circle" />
      <FinanceValueCard title="Total Credits" :value="totalCredits" rows="2-row" format="currency" color="positive" icon="fas fa-minus-circle" />
    </div>

    <!-- Balance Status -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FinanceValueCard title="Balance Status" :value="isBalanced ? 'Balanced ✓' : 'Unbalanced ✗'" rows="2-row" :color="isBalanced ? 'positive' : 'negative'" icon="fas fa-balance-scale" />
      <FinanceValueCard title="Difference" :value="balanceDifference" rows="2-row" format="currency" color="auto" :max-good="0" :max-warning="100" icon="fas fa-calculator" />
    </div>

    <div class="bg-white dark:bg-gray-800 rounded border dark:border-gray-700 mb-6">
      <div class="p-4 border-b bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold dark:text-white">Journal Entries</h3>
      </div>
      
      <div v-if="filteredJournalEntries.length > 0" class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700 text-left">
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Date</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Description</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Account</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Debit</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Credit</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="entry in filteredJournalEntries" :key="entry.id">
              <tr v-for="line in entry.lines" :key="line.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-gray-100" :style="store.scalingStyles.tableRowHeight">
                <td class="p-3 border dark:border-gray-600">{{ entry.timestamp }}</td>
                <td class="p-3 border dark:border-gray-600">{{ entry.description }}</td>
                <td class="p-3 border dark:border-gray-600">{{ line.account }}</td>
                <td class="p-3 border dark:border-gray-600">{{ line.type === 'debit' ? line.amount : '' }}</td>
                <td class="p-3 border dark:border-gray-600">{{ line.type === 'credit' ? line.amount : '' }}</td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
      <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
        No journal entries for this period
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded border dark:border-gray-700 mb-6">
      <div class="p-4 border-b bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold dark:text-white">Trial Balance</h3>
      </div>
      
      <div v-if="Object.keys(trialBalance).length > 0" class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700 text-left">
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Account</th>
              <th class="p-3 border dark:border-gray-600 dark:text-gray-200">Balance</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(balance, account) in trialBalance" :key="account" class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-gray-100" :style="store.scalingStyles.tableRowHeight">
              <td class="p-3 border dark:border-gray-600">{{ account }}</td>
              <td class="p-3 border dark:border-gray-600">{{ balance }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
        No trial balance available
      </div>
    </div>

    <div v-if="showNewEntryForm">
      <div class="fixed inset-0 z-20 bg-black opacity-20 dark:opacity-75" @click="showNewEntryForm = false"></div>
      <div class="z-30 fixed top-1/2 left-1/2 w-full max-w-4xl h-[850px] opacity-95 bg-gradient-to-bl rounded-sm shadow-lg shadow-gray-400 focus:outline focus:outline-2 focus:outline-purple-500 motion-safe:hover:scale-[1.01] transition-all duration-250 transform -translate-x-1/2 -translate-y-1/2 p-6 pt-10 bg-gray-100 text-gray-600 from-gray-200/50 via-transparent dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50 dark:text-gray-300 dark:shadow-gray-600">
        <div class="h-full flex flex-col">
          <div class="flex-1 overflow-y-auto">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold mb-4 dark:text-white">New Journal Entry</h3>
        <form @submit.prevent="handleNewEntry">
          <div class="mb-4">
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
            <input v-model="newEntry.description" required class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" placeholder="Enter description" />
          </div>
          
          <div class="mb-4">
            <label :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Entry Lines</label>
            <div class="space-y-3">
              <div v-for="(line, index) in newEntry.lines" :key="index" class="grid grid-cols-4 gap-2 items-center">
                <FinanceDropdown 
                  v-model="line.account" 
                  :options="['', ...accounts]"
                  placeholder="Select Account"
                  variant="outline"
                  size="normal"
                  full-width
                />
                <FinanceDropdown 
                  v-model="line.type" 
                  :options="entryTypeOptions"
                  option-label="label"
                  option-value="value"
                  placeholder="Debit/Credit"
                  variant="outline"
                  size="normal"
                  full-width
                />
                <input type="number" v-model="line.amount" step="0.01" required class="border rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600" placeholder="Amount" />
                <button type="button" @click="removeLine(index)" v-if="newEntry.lines.length > 2" class="px-2 py-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                  Remove
                </button>
              </div>
            </div>
            <button type="button" @click="addLine" :style="scalingStyles.smallFontSize" class="mt-2 px-3 py-1 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded">+ Add Line</button>
          </div>
          
          <!-- Balance Validation Display -->
          <div class="mb-4 p-3 rounded border" :class="{ 'border-green-200 bg-green-50 dark:border-green-600 dark:bg-green-900/20': isBalanced, 'border-red-200 bg-red-50 dark:border-red-600 dark:bg-red-900/20': !isBalanced }">
            <div class="grid grid-cols-2 gap-4" :style="scalingStyles.smallFontSize">
              <div>
                <span class="font-medium dark:text-gray-200">Total Debits:</span> 
                <span class="ml-2 dark:text-gray-100">{{ formatCurrency(totalDebits) }}</span>
              </div>
              <div>
                <span class="font-medium dark:text-gray-200">Total Credits:</span> 
                <span class="ml-2 dark:text-gray-100">{{ formatCurrency(totalCredits) }}</span>
              </div>
            </div>
            <div v-if="!isBalanced" class="mt-2 text-red-600 dark:text-red-400 font-medium">
              Difference: {{ formatCurrency(balanceDifference) }}
            </div>
            <div class="mt-2 font-medium dark:text-gray-200">
              Status: <span :class="{ 'text-green-600 dark:text-green-400': isBalanced, 'text-red-600 dark:text-red-400': !isBalanced }">
                {{ isBalanced ? 'Balanced ✓' : 'Unbalanced ✗' }}
              </span>
            </div>
          </div>
          
          <div class="flex justify-end gap-2">
            <button type="button" @click="showNewEntryForm = false" class="px-3 py-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
            <button type="submit" :disabled="isLoading || !isBalanced || !canSubmit" class="px-3 py-2 rounded bg-indigo-600 text-white disabled:opacity-50 hover:bg-indigo-700">Save Entry</button>
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
  grid-template-columns: repeat(2, 1fr);
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