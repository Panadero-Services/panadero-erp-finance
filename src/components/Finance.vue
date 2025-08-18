<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

import GeneralLedger from './GeneralLedger.vue';
import AccountsPayable from './AccountsPayable.vue';
import AccountsReceivable from './AccountsReceivable.vue';
import CashFlow from './CashFlow.vue';
import TaxManagement from './TaxManagement.vue';

import FixedAssets from './FixedAssets.vue';
import Reporting from './Reporting.vue';
import BudgetingForecasting from './BudgetingForecasting.vue';
import ComplianceAudit from './ComplianceAudit.vue';
import InfoBoard from './InfoBoard.vue';
import FinanceDashboard from './dashboard/FinanceDashboard.vue';
import FinanceLayout from './layout/FinanceLayout.vue';

import { useFinanceStore } from '../stores/financeStore';
import { useInfoBoxes } from '../composables/useInfoBoxes';
import FinanceSettingsPanel from './FinanceSettingsPanel.vue';
import { darkModeClasses } from '../utils/darkMode';

const store = useFinanceStore();

// Tabs: add Dashboard as default
const tabs = [
  { id: 'infoboard', label: 'ERP Finance', icon: 'fas fa-info-circle', color: 'text-blue-500', component: InfoBoard },
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', color: 'text-indigo-500', component: FinanceDashboard },
  { id: 'general-ledger', label: 'General Ledger', icon: 'fas fa-book-open', color: 'text-emerald-500', component: GeneralLedger },
  { id: 'accounts-payable', label: 'Accounts Payable', icon: 'fas fa-credit-card', color: 'text-red-500', component: AccountsPayable },
  { id: 'accounts-receivable', label: 'Accounts Receivable', icon: 'fas fa-hand-holding-usd', color: 'text-green-500', component: AccountsReceivable },
  { id: 'cash-flow', label: 'Cash Flow', icon: 'fas fa-chart-line', color: 'text-purple-500', component: CashFlow },
  { id: 'fixed-assets', label: 'Fixed Assets', icon: 'fas fa-industry', color: 'text-orange-500', component: FixedAssets },
  { id: 'reporting', label: 'Reporting', icon: 'fas fa-chart-bar', color: 'text-cyan-600', component: Reporting },
  { id: 'budgeting', label: 'Budgeting', icon: 'fas fa-chart-pie', color: 'text-pink-600', component: BudgetingForecasting },
  { id: 'compliance', label: 'Compliance', icon: 'fas fa-shield-alt', color: 'text-teal-600', component: ComplianceAudit },
  { id: 'tax-management', label: 'Tax Management', icon: 'fas fa-file-invoice-dollar', color: 'text-amber-500', component: TaxManagement },
];

const activeTab = ref('dashboard');

// Use the info boxes composable
const {
  activeInfo,
  packageTables,
  sharedEntities,
  versionUpdates,
  moduleDescription,
  navigateToEntity,
  toggleInfoBox,
  getEntityColorClasses
} = useInfoBoxes();

// Load settings on mount
onMounted(() => {
  store.loadSettings();
  console.log('Finance component mounted, settings loaded:', store.settings);
});

// Simplified activeComponent computed - dashboard now has its own component
const activeComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.component : GeneralLedger;
});

// Handle tab switching from infobox navigation
function handleTabSwitch(event) {
  const { tabId } = event.detail;
  console.log('🔄 Tab switch triggered:', tabId, 'from event:', event);
  if (tabs.find(t => t.id === tabId)) {
    activeTab.value = tabId;
  }
}

function handleSettingsChanged(change) {
  if (change.type === 'fontSize') {
    // Font size changes are handled automatically by the useStyling composable
    console.log('Font size changed to:', change.value);
  } else if (change.type === 'settings') {
    // Handle other settings changes
    console.log('Settings changed:', change.value);
    
    // Apply dark mode setting if changed
    if (change.value.darkMode !== undefined) {
      // This could trigger a global dark mode change
      console.log('Dark mode setting:', change.value.darkMode);
    }
  }
}

// Set up event listeners
onMounted(() => {
  window.addEventListener('switchFinanceTab', handleTabSwitch);
});

onUnmounted(() => {
  window.removeEventListener('switchFinanceTab', handleTabSwitch);
});


</script>

<template>
  <FinanceLayout :active-tab="activeTab" :tabs="tabs" @tab-change="activeTab = $event">
    <component :is="activeComponent" @tab-change="activeTab = $event" />
  </FinanceLayout>
  
  <!-- Settings Panel -->
  <FinanceSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>

