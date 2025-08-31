<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'
import FinanceValueCard from '../ui/FinanceValueCard.vue';

const store = useFinanceStore()
const { fontSizes, scalingStyles, spacing } = useScaling()

// All computed properties for dashboard data
const currentPeriod = computed(() => store.currentPeriod);

const journalEntriesCount = computed(() => {
  return store.journalEntries.filter(e => e.period === currentPeriod.value).length;
});

const receivablesOutstanding = computed(() => {
  return store.receivables.reduce((sum, r) => sum + Math.max(0, (Number(r.amount)||0) - (Number(r.received_amount)||0)), 0);
});

const payablesOutstanding = computed(() => {
  return store.payables.reduce((sum, p) => sum + Math.max(0, (Number(p.amount)||0) - (Number(p.paid_amount)||0)), 0);
});

const netCashThisPeriod = computed(() => {
  // inflow positive, outflow negative for current period
  const [y,m] = String(currentPeriod.value || '').split('-');
  if (!y || !m) return 0;
  
  const periodCashFlows = store.cashFlowTransactions.filter(t => {
    const [ty, tm] = String(t.period || '').split('-');
    return ty === y && tm === m;
  });
  
  return periodCashFlows.reduce((sum, t) => {
    return sum + (t.type === 'inflow' ? Number(t.amount) : -Number(t.amount));
  }, 0);
});

const recentJournalEntries = computed(() => {
  return [...store.journalEntries]
    .sort((a,b) => new Date(b.timestamp) - new Date(a.timestamp))
    .slice(0, 5);
});

function daysDiff(dateStr) {
  const d = new Date(dateStr);
  const now = new Date();
  return Math.floor((now - d) / (1000*60*60*24));
}

const overdueAP = computed(() => {
  const today = new Date();
  return store.payables
    .filter(p => new Date(p.due_date) < today && (Number(p.amount)||0) > (Number(p.paid_amount)||0))
    .sort((a,b) => new Date(a.due_date) - new Date(b.due_date))
    .slice(0, 5);
});

const overdueAR = computed(() => {
  const today = new Date();
  return store.receivables
    .filter(r => new Date(r.due_date) < today && (Number(r.amount)||0) > (Number(r.received_amount)||0))
    .sort((a,b) => new Date(a.due_date) - new Date(b.due_date))
    .slice(0, 5);
});

const cashByCategory = computed(() => {
  const cat = {};
  const [y,m] = String(currentPeriod.value || '').split('-');
  if (!y || !m) return [];
  
  const periodCashFlows = store.cashFlowTransactions.filter(t => {
    const [ty, tm] = String(t.period || '').split('-');
    return ty === y && tm === m;
  });
  
  periodCashFlows.forEach(t => {
    if (!cat[t.category_name]) cat[t.category_name] = 0;
    cat[t.category_name] += t.type === 'inflow' ? Number(t.amount) : -Number(t.amount);
  });
  
  return Object.entries(cat).map(([name, value]) => ({ name, value }));
});

function fmt(n){ return new Intl.NumberFormat('en-US',{ style:'currency', currency:'USD'}).format(n||0); }
function shortDate(d){ return new Date(d).toLocaleDateString(); }

// Emit events for tab navigation
const emit = defineEmits(['tab-change']);

const navigateToTab = (tabId) => {
  emit('tab-change', tabId);
};
</script>

<template>
  <div class="space-y-6">
    <!-- KPI cards -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <FinanceValueCard title="Current Period" :value="currentPeriod" :subtitle="`Journal Entries: ${journalEntriesCount}`" rows="3-row" color="info" icon="fas fa-calendar" />

      <FinanceValueCard title="Receivables Outstanding" :value="receivablesOutstanding" subtitle="Top overdue AR below" rows="3-row" format="currency" color="auto" :max-good="15000" :max-warning="30000" icon="fas fa-arrow-circle-up" trend="up" />

      <FinanceValueCard title="Payables Outstanding" :value="payablesOutstanding" subtitle="Top overdue AP below" rows="3-row" format="currency" color="auto" :max-good="10000" :max-warning="25000" icon="fas fa-arrow-circle-down" trend="down" />

      <FinanceValueCard title="Net Cash (Period)" :value="netCashThisPeriod" subtitle="Inflow − Outflow" rows="3-row" format="currency" color="auto" :min-good="0" :min-warning="-5000" icon="fas fa-chart-line" />
    </div>

    <!-- Grids: recent JE, overdue AP/AR, cash categories -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 xl:grid-cols-3 gap-4">
      <!-- Recent Journal Entries -->
      <div :style="[scalingStyles.cardPadding, scalingStyles.borderRadius]" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Recent Journal Entries</h3>
          <button :style="[scalingStyles.buttonPadding, { fontSize: `${fontSizes.base - 2}px` }]" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300" @click="navigateToTab('general-ledger')">Open GL</button>
        </div>
        <div class="divide-y dark:divide-gray-700">
          <div v-for="e in recentJournalEntries" :key="e.id" class="py-3">
            <div class="flex items-center justify-between">
              <div :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-800 dark:text-gray-200">{{ e.description }}</div>
              <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">{{ shortDate(e.timestamp) }}</div>
            </div>
            <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="mt-1 text-gray-500 dark:text-gray-400">Lines: {{ e.lines.length }} • Period: {{ e.period }}</div>
          </div>
          <div v-if="recentJournalEntries.length === 0" :style="{ fontSize: `${fontSizes.base - 2}px` }" class="py-6 text-center text-gray-500 dark:text-gray-400 italic">No entries</div>
        </div>
      </div>

      <!-- Overdue AP -->
      <div :style="[scalingStyles.cardPadding, scalingStyles.borderRadius]" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Overdue Payables</h3>
          <button :style="[scalingStyles.buttonPadding, { fontSize: `${fontSizes.base - 2}px` }]" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300" @click="navigateToTab('accounts-payable')">Open AP</button>
        </div>
        <div class="divide-y dark:divide-gray-700">
          <div v-for="p in overdueAP" :key="p.id" class="py-3">
            <div class="flex items-center justify-between">
              <div :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-800 dark:text-gray-200">{{ p.vendor_name }} — {{ p.invoice_no }}</div>
              <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-rose-600 dark:text-red-400 font-semibold">+{{ daysDiff(p.due_date) }}d</div>
            </div>
            <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="mt-1 text-gray-500 dark:text-gray-400">Due: {{ shortDate(p.due_date) }} • Outstanding: {{ fmt((Number(p.amount)||0) - (Number(p.paid_amount)||0)) }}</div>
          </div>
          <div v-if="overdueAP.length === 0" :style="{ fontSize: `${fontSizes.base - 2}px` }" class="py-6 text-center text-gray-500 dark:text-gray-400 italic">No overdue payables</div>
        </div>
      </div>

      <!-- Overdue AR -->
      <div :style="[scalingStyles.cardPadding, scalingStyles.borderRadius]" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
          <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Overdue Receivables</h3>
          <button :style="[scalingStyles.buttonPadding, { fontSize: `${fontSizes.base - 2}px` }]" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300" @click="navigateToTab('accounts-receivable')">Open AR</button>
        </div>
        <div class="divide-y dark:divide-gray-700">
          <div v-for="r in overdueAR" :key="r.id" class="py-3">
            <div class="flex items-center justify-between">
              <div :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-800 dark:text-gray-200">{{ r.customer_name }} — {{ r.invoice_no }}</div>
              <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-rose-600 dark:text-red-400 font-semibold">+{{ daysDiff(r.due_date) }}d</div>
            </div>
            <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="mt-1 text-gray-500 dark:text-gray-400">Due: {{ shortDate(r.due_date) }} • Outstanding: {{ fmt((Number(r.amount)||0) - (Number(r.received_amount)||0)) }}</div>
          </div>
          <div v-if="overdueAR.length === 0" :style="{ fontSize: `${fontSizes.base - 2}px` }" class="py-6 text-center text-gray-500 dark:text-gray-400 italic">No overdue receivables</div>
        </div>
      </div>
    </div>

    <!-- Cash by Category -->
    <div :style="[scalingStyles.cardPadding, scalingStyles.borderRadius]" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
      <div class="mb-3 flex items-center justify-between">
        <h3 :style="{ fontSize: `${fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">Cash Flow by Category ({{ currentPeriod }})</h3>
        <button :style="[scalingStyles.buttonPadding, { fontSize: `${fontSizes.base - 2}px` }]" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300" @click="navigateToTab('cash-flow')">Open Cash Flow</button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <div v-for="row in cashByCategory" :key="row.name" :style="[scalingStyles.borderRadius, { padding: '12px' }]" class="border dark:border-gray-600">
          <div :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">{{ row.name }}</div>
          <div :style="{ fontSize: `${fontSizes.base + 6}px` }" class="mt-1 font-semibold" :class="row.value>=0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-red-400'">{{ fmt(row.value) }}</div>
        </div>
        <div v-if="cashByCategory.length === 0" :style="{ fontSize: `${fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 italic p-3">No cash flow data</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Dashboard-specific styles */
</style>

