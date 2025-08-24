// Workflow Management Package - Main Export
// @version 1.0.10
// @description Exports main workflow components for external use

// Main External Components
export { default as WorkflowWrapper } from './src/Wrapper.vue'
export { default as WorkflowDashboard } from './src/Dashboard.vue'
export { default as WorkflowDemo } from './src/Demo.vue'
export { default as WorkflowManager } from './src/Manager.vue'

// Internal Components (for advanced usage)
export { default as WorkflowCard } from './src/components/Card.vue'
export { default as WorkflowForm } from './src/components/WorkflowForm.vue'
export { default as WorkflowModal } from './src/components/Modal.vue'
export { default as WorkflowStepper } from './src/components/WorkflowStepper.vue'
export { default as WorkflowInfo } from './src/components/Info.vue'
export { default as WorkflowStatistics } from './src/components/Statistics.vue'
export { default as WorkflowDetailModal } from './src/components/DetailModal.vue'
export { default as ActiveWorkflowCard } from './src/components/ActiveCard.vue'
export { default as WorkflowTemplate } from './src/components/Template.vue'

// Composables
export { useWorkflowStore } from './src/composables/workflowStore.js'
export { useWorkflowDashboard } from './src/composables/useWorkflowDashboard.js'
export { useWorkflowSettings } from './src/composables/useWorkflowSettings.js'

// Package Info
export const packageInfo = {
  name: 'panadero-workflow',
  version: '1.0.10',
  description: 'Financial ERP Workflow Package System',
  main: 'WorkflowDashboard.vue'
}
