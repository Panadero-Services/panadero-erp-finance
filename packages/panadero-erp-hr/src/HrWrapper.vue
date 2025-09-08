<!--
  HR Wrapper Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Wrapper component for ERP HR management
-->
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useHrStore } from './stores/hrStore';
import HrLayout from './components/layout/HrLayout.vue';
import InfoBoard from './components/InfoBoard.vue';
import HrDashboard from './components/dashboard/HrDashboard.vue';
import EmployeeManagement from './components/EmployeeManagement.vue';
import Recruitment from './components/Recruitment.vue';
import VacancyManagement from './components/VacancyManagement.vue';
import DepartmentManagement from './components/DepartmentManagement.vue';
import HrReporting from './components/HrReporting.vue';
import AgentPortal from './components/AgentPortal.vue';

// Import the Framework Settings Panel from shared location
import FrameworkSettingsPanel from '../../shared/components/FrameworkSettingsPanel.vue';

const store = useHrStore();

const tabs = [
  { id: 'infoboard', label: 'ERP HR', icon: 'fas fa-info-circle', color: 'text-green-500', component: InfoBoard },
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', color: 'text-indigo-500', component: HrDashboard },
  { id: 'employees', label: 'Employee Management', icon: 'fas fa-users', color: 'text-blue-500', component: EmployeeManagement },
  { id: 'recruitment', label: 'Recruitment', icon: 'fas fa-user-plus', color: 'text-purple-500', component: Recruitment },
  { id: 'vacancies', label: 'Vacancies', icon: 'fas fa-briefcase', color: 'text-orange-500', component: VacancyManagement },
  { id: 'departments', label: 'Departments', icon: 'fas fa-building', color: 'text-cyan-500', component: DepartmentManagement },
  { id: 'reports', label: 'HR Reports', icon: 'fas fa-chart-bar', color: 'text-pink-500', component: HrReporting },
  { id: 'agent-portal', label: 'Agent Portal', icon: 'fas fa-robot', color: 'text-teal-500', component: AgentPortal }
];

const activeTab = ref('dashboard');

const activeComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.component : HrDashboard;
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
  window.addEventListener('switchHrTab', handleTabSwitch);
});

onUnmounted(() => {
  window.removeEventListener('switchHrTab', handleTabSwitch);
});
</script>

<template>
  <HrLayout :active-tab="activeTab" :tabs="tabs" @tab-change="activeTab = $event">
    <component :is="activeComponent" @tab-change="activeTab = $event" />
  </HrLayout>
  
  <!-- Framework Settings Panel (no circular reference) -->
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>
