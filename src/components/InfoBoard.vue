<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useFinanceStore } from '../stores/financeStore.js'
import { useScaling } from '../../../shared/composables/useScaling.js'

import InfoSection from './sections/InfoSection.vue'
import InfoSection2 from './sections/InfoSection2.vue'
import UIElementsSection from './sections/UIElementsSection.vue'

//import WorkflowInfoSection from './sections/WorkflowInfoSection.vue'
//import WorkflowManagementSection from './sections/WorkflowManagementSection.vue'
//import WorkflowSection from './sections/WorkflowSection.vue'

import APIsSection from './sections/APIsSection.vue'

const store = useFinanceStore()
const { fontSizes, scalingStyles, spacing } = useScaling()

// Tab management
const activeTab = ref('info')

// Permissions for each tab
const permissions = ref({
  info: true,
  uiElements: true,
  workflows: true,
  apis: false // Future feature
})

// Check if user has permission for tab
const hasPermission = (tab) => permissions.value[tab] || false

// Available tabs
const tabs = [
  { id: 'info', name: 'Info', icon: 'fas fa-info-circle', permission: 'info' },
  { id: 'info2', name: 'Info2', icon: 'fas fa-info-circle', permission: 'info' },
  { id: 'uiElements', name: 'UI Elements', icon: 'fas fa-palette', permission: 'uiElements' },
  //{ id: 'workflowInfo', name: 'Workflow Info', icon: 'fas fa-info-circle', permission: 'info' },
  //{ id: 'workflowSection', name: 'Workflows', icon: 'fas fa-sitemap', permission: 'workflows' },
  //{ id: 'apis', name: 'APIs', icon: 'fas fa-code', permission: 'apis' }
  { id: 'apis', name: 'APIs', icon: 'fas fa-code', permission: 'info' }
]

// Filter tabs based on permissions
const availableTabs = computed(() => tabs.filter(tab => hasPermission(tab.permission)))

// Switch tabs
const switchTab = (tabId) => {
  if (hasPermission(tabs.find(t => t.id === tabId)?.permission)) {
    activeTab.value = tabId
  }
}
</script>

<template>
  <div class="px-1 -mt-3">
    <!-- Tab Navigation -->
    <div class="mb-6">
      <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="InfoBoard Tabs">
          <button
            v-for="tab in availableTabs"
            :key="tab.id"
            @click="switchTab(tab.id)"
            :class="[
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === tab.id
                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'
            ]"
            :style="scalingStyles.textFontSize"
          >
            <i :class="tab.icon" class="mr-2"></i>
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Info Tab -->
      <InfoSection v-if="activeTab === 'info'" />

      <!-- Info Tab -->
      <InfoSection2 v-if="activeTab === 'info2'" />



      <!-- UI Elements Tab -->
      <UIElementsSection v-else-if="activeTab === 'uiElements'" />

      <!-- Workflow Info Tab 
      <WorkflowInfoSection v-else-if="activeTab === 'workflowInfo'" />-->

      <!-- Workflow System Tab 
      <WorkflowSection v-else-if="activeTab === 'workflowSection'" />-->

      <!-- APIs Tab -->
      <APIsSection v-else-if="activeTab === 'apis'" :has-access="permissions.apis" />
    </div>
  </div>
</template>

<style scoped>
/* InfoBoard wrapper specific styles */
.tab-content {
  min-height: 400px;
}
</style>