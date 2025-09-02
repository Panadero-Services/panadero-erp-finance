<!--
  Workflow Wrapper Component
  @version 1.1.4
  @date 30-Aug-2025
  @description Wrapper component for workflow management
-->
<script setup>
import { onMounted, ref, computed } from 'vue'
import Layout from './components/sections/WorkflowLayout.vue';
import InfoBoard from './components/InfoBoard.vue'
import WorkflowDashboard from './WorkflowDashboard.vue'
import WorkflowDemo from './WorkflowDemo.vue'
//import Statistics from './components/Statistics.vue'

// Import the Framework Settings Panel from shared location
import FrameworkSettingsPanel from '../../shared/components/FrameworkSettingsPanel.vue';

import { useWorkflowStore } from './composables/workflowStore';
const workflowStore = useWorkflowStore();

import { useScalingV2 } from '../../shared/composables/useScalingV2.js'
const scaling = useScalingV2()

// Tabs: add Dashboard as default
const tabs = [
  { id: 'infoboard', label: 'Features Workflow', icon: 'fas fa-info-circle', color: 'text-blue-500', component: InfoBoard },
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', color: 'text-orange-500', component: WorkflowDashboard },
  { id: 'demo', label: 'Demo (soon)', icon: 'fas fa-chart-line', color: 'text-purple-500', component: WorkflowDemo },
  //{ id: 'landing', label: 'Landing (soon)', icon: 'fas fa-gauge', color: 'text-indigo-500', component: WorkflowDashboard },
];

const activeTab = ref('dashboard');

const activeComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.component : InfoBoard;
});

function handleTabSwitch(event) {
  const { tabId } = event.detail;
  if (tabs.find(t => t.id === tabId)) {
    activeTab.value = tabId;
  }
}

onMounted(() => {
  console.debug('Workflow Package: Wrapper.vue mounted')
  console.debug('Store object:', workflowStore)

})
</script>

<template>

 <Layout :active-tab="activeTab" :tabs="tabs" @tab-change="activeTab = $event">
    <component :is="activeComponent" 
        :workflowStore="workflowStore" 
        :scaling="scaling" 
        @tab-change="activeTab = $event" />
  </Layout>

    <!-- Framework Settings Panel (no circular reference) -->
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>

<style scoped>
.workflow-wrapper {
  /* Wrapper styles */
}
</style>