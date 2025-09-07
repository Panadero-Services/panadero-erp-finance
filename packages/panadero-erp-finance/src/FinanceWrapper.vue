<!--
  Workflow Wrapper Component
  @version 1.1.1
  @date 30-Aug-2025
  @description Wrapper component for ERP.finance management
-->
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useFinanceStore } from './stores/financeStore';
import FinanceLayout from './components/layout/FinanceLayout.vue';
import InfoBoard from './components/InfoBoard.vue';
import FinanceDashboard from './components/dashboard/FinanceDashboard.vue';
import GeneralLedger from './components/GeneralLedger.vue';
import AccountsPayable from './components/AccountsPayable.vue';
import AccountsReceivable from './components/AccountsReceivable.vue';
import CashFlow from './components/CashFlow.vue';
import TaxManagement from './components/TaxManagement.vue';
import FixedAssets from './components/FixedAssets.vue';
import Reporting from './components/Reporting.vue';
import BudgetingForecasting from './components/BudgetingForecasting.vue';
import ComplianceAudit from './components/ComplianceAudit.vue';

// Import the Framework Settings Panel from shared location
import FrameworkSettingsPanel from '../../shared/components/FrameworkSettingsPanel.vue';

const store = useFinanceStore();

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
  { id: 'tax-management', label: 'Tax Management', icon: 'fas fa-file-invoice-dollar', color: 'text-amber-500', component: TaxManagement }
];

const activeTab = ref('dashboard');

const activeComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.component : GeneralLedger;
});

function handleTabSwitch(event) {
  const { tabId } = event.detail;
  if (tabs.find(t => t.id === tabId)) {
    activeTab.value = tabId;
  }
}

// Add the missing function
function handleSettingsChanged(change) {
  if (change.type === 'fontSize') {
    console.debug('Framework font size changed to:', change.value);
  } else if (change.type === 'settings') {
    console.debug('Framework settings changed:', change.value);
  }
}

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
  
  <!-- Framework Settings Panel (no circular reference) -->
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>