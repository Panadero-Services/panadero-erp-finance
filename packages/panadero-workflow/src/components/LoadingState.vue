<!--
  Loading State Component
  @version 1.1.0
  @description Displays loading state and configuration workflows
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  loadingConfigWorkflows: {
    type: Boolean,
    default: false
  },
  configWorkflows: {
    type: Array,
    default: () => []
  },
  workflowModules: {
    type: Array,
    default: () => []
  },
  workflowStore: {
    type: Object,
    required: true
  },
  scaling: {
    type: Object,
    required: true
  }
})

// Settings
const settings = useWorkflowSettings()

// Computed properties
const isLoading = computed(() => props.loadingConfigWorkflows)
const hasWorkflows = computed(() => props.configWorkflows.length > 0)
const hasModules = computed(() => props.workflowModules.length > 0)

// Dynamic font sizes - ONLY changing styling references
const _h3 = computed(() => props.scaling.font.heading) // WAS: `${settings.fontSizesComputed.value.h3}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`

// Get color classes from store
const getColorClasses = (colorType, element) => {
  return props.workflowStore.getColorClasses(colorType, element)
}
</script>

<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p :style="{ fontSize: _body }" class="text-gray-600 dark:text-gray-400">
        Loading workflow configuration...
      </p>
    </div>

    <!-- No Workflows State -->
    <div v-else-if="!hasWorkflows && !hasModules" class="text-center py-12">
      <div class="text-gray-400 dark:text-gray-500 mb-4">
        <i class="fas fa-folder-open text-6xl"></i>
      </div>
      <h3 :style="{ fontSize: _h3 }" class="text-gray-900 dark:text-white mb-2">
        No Workflows Available
      </h3>
      <p :style="{ fontSize: _body }" class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
        No workflow templates or modules are currently configured. Please check your configuration or contact your administrator.
      </p>
    </div>

    <!-- Configuration Workflows -->
    <div v-else-if="hasWorkflows" class="space-y-4">
      <h3 :style="{ fontSize: _h3 }" class="text-gray-900 dark:text-white">
        Configuration Workflows
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="workflow in configWorkflows" 
          :key="workflow.id"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
          <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-2">
            {{ workflow.name }}
          </h4>
          <p :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">
            {{ workflow.description }}
          </p>
        </div>
      </div>
    </div>

    <!-- Workflow Modules -->
    <div v-else-if="hasModules" class="space-y-4">
      <h3 :style="{ fontSize: _h3 }" class="text-gray-900 dark:text-white">
        Available Modules
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="module in workflowModules" 
          :key="module.id"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
          <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-2">
            {{ module.name }}
          </h4>
          <p :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">
            {{ module.description }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
