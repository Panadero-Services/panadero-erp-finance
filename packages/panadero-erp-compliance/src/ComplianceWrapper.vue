<!--
  Compliance Wrapper Component
  @version 1.0.0
  @date 15-Jan-2025
  @description Wrapper component for ERP Compliance management
-->
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useComplianceStore } from './stores/complianceStore';
import ComplianceLayout from './components/layout/ComplianceLayout.vue';
import InfoBoard from './components/InfoBoard.vue';
import ComplianceDashboard from './components/dashboard/ComplianceDashboard.vue';
import RegulatoryCompliance from './components/RegulatoryCompliance.vue';
import AuditTrails from './components/AuditTrails.vue';
import RiskManagement from './components/RiskManagement.vue';
import PolicyManagement from './components/PolicyManagement.vue';
import ComplianceReporting from './components/ComplianceReporting.vue';
import RootCauseAnalysis from './components/RootCauseAnalysis.vue';
import AgentPortal from './components/AgentPortal.vue';

// Import the Framework Settings Panel from shared location
import { FrameworkSettingsPanel } from 'panadero-shared-components';

const store = useComplianceStore();

const tabs = [
  { id: 'infoboard', label: 'ERP Compliance', icon: 'fas fa-info-circle', color: 'text-teal-500', component: InfoBoard },
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', color: 'text-indigo-500', component: ComplianceDashboard },
  { id: 'regulatory', label: 'Regulatory Compliance', icon: 'fas fa-gavel', color: 'text-blue-500', component: RegulatoryCompliance },
  { id: 'audit', label: 'Audit Trails', icon: 'fas fa-clipboard-check', color: 'text-emerald-500', component: AuditTrails },
  { id: 'risk', label: 'Risk Management', icon: 'fas fa-exclamation-triangle', color: 'text-red-500', component: RiskManagement },
  { id: 'policies', label: 'Policy Management', icon: 'fas fa-file-contract', color: 'text-purple-500', component: PolicyManagement },
  { id: 'reports', label: 'Compliance Reports', icon: 'fas fa-chart-line', color: 'text-green-500', component: ComplianceReporting },
  { id: 'rca', label: 'Root Cause Analysis', icon: 'fas fa-search', color: 'text-orange-500', component: RootCauseAnalysis },
  { id: 'agent-portal', label: 'Agent Portal', icon: 'fas fa-robot', color: 'text-cyan-500', component: AgentPortal }
];

const activeTab = ref('dashboard');

const activeComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.component : ComplianceDashboard;
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
    store.updateSettings({ fontSize: change.value });
  } else if (change.type === 'settings') {
    console.debug('Framework settings changed:', change.value);
    store.updateSettings(change.value);
  }
}

onMounted(() => {
  window.addEventListener('switchComplianceTab', handleTabSwitch);
});

onUnmounted(() => {
  window.removeEventListener('switchComplianceTab', handleTabSwitch);
});
</script>

<template>
  <ComplianceLayout :active-tab="activeTab" :tabs="tabs" @tab-change="activeTab = $event">
    <component :is="activeComponent" @tab-change="activeTab = $event" />
  </ComplianceLayout>
  
  <!-- Framework Settings Panel (no circular reference) -->
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>
