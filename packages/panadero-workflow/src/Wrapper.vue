<script setup>
import { watch, onMounted } from 'vue'
import { useWorkflowStore } from './composables/workflowStore.js'
import WorkflowDashboard from './Dashboard.vue'
import WorkflowStatistics from './components/Statistics.vue'

// Props from finance module
const props = defineProps({
  settings: {
    type: Object,
    required: true,
    default: () => ({
      fontSize: 14,
      fontSizes: {},
      scalingStyles: {},
      darkMode: true,
      compactLayout: false
    })
  }
})

// Workflow store
const workflowStore = useWorkflowStore()

// Bind finance settings to workflow store (make workflow reactive)
const bindSettings = () => {
    workflowStore.updateSettings(props.settings)
}

// Watch for settings changes and update workflow store
watch(() => props.settings, bindSettings, { deep: true, immediate: true })

onMounted(() => {
  console.debug('Workflow Package: Wrapper.vue mounted')
})
</script>

<template>
  <div class="workflow-wrapper">

      <!-- Title  -->
      <h1 :style="{ fontSize: `${workflowStore.settings.fontSizes.base + 8}px` }" class="font-bold text-gray-900 dark:text-white mb-8">
        <i class="fas fa-sitemap mr-3 text-indigo-600 dark:text-indigo-400"></i>
        Workflow Management System
      </h1>


    <!-- Statistics Cards -->
    <WorkflowStatistics :stats="workflowStats"/>

    <!-- Main workflow dashboard with reactive settings -->
    <WorkflowDashboard />
  </div>
</template>

<style scoped>
.workflow-wrapper {
  /* Wrapper styles */
}
</style>