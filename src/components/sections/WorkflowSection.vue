<script setup>
import { computed } from 'vue'
import { useFinanceStore } from '../../stores/financeStore.js'
import { WorkflowWrapper, WorkflowDemo, WorkflowDashboard, WorkflowStatistics } from '../../../../panadero-workflow'

// Finance store for reactive settings
const financeStore = useFinanceStore()

// Reactive settings to pass to workflow module
const workflowSettings = computed(() => ({
  fontSize: financeStore.settings.fontSize,
  fontSizes: financeStore.fontSizes,
  scalingStyles: financeStore.scalingStyles,
  darkMode: financeStore.settings.darkMode,
  compactLayout: financeStore.settings.compactLayout
}))
</script>

<template>
  <div class="workflow-section">

    <!-- Pass reactive finance settings to workflow module -->
    <WorkflowWrapper :settings="workflowSettings" />
  </div>
</template>

<style scoped>
/* Workflow section specific styles */
.workflow-section {
  /* Container for workflow module interface */
}
</style>