// Workflow Management Package - Main Export
// @version 1.0.10
// @description Exports main workflow components for external use

// Main External Components
export { default as WorkflowWrapper } from './src/WorkflowWrapper.vue'
export { default as WorkflowDashboard } from './src/WorkflowDashboard.vue'
export { default as WorkflowDemo } from './src/WorkflowDemo.vue'
export { default as WorkflowManager } from './src/WorkflowManager.vue'

// Internal Components (for advanced usage)
export { default as WorkflowCard } from './src/components/Card.vue'
export { default as WorkflowForm } from './src/components/modal1/WorkflowForm.vue'
export { default as WorkflowModal } from './src/components/modal1/Modal.vue'
export { default as WorkflowStepper } from './src/components/modal1/WorkflowStepper.vue'
export { default as WorkflowInfo } from './src/components/modal1/ModalInfo.vue'
export { default as WorkflowStatistics } from './src/components/Statistics.vue'
export { default as ActiveWorkflowCard } from './src/components/ActiveCard.vue'
export { default as WorkflowTemplate } from './src/components/Template.vue'

// Step Type Components
export { default as WorkflowApproval } from './src/components/modal1/WorkflowApproval.vue'
export { default as WorkflowChecklist } from './src/components/modal1/WorkflowChecklist.vue'
export { default as WorkflowEntitySelection } from './src/components/modal1/WorkflowEntitySelection.vue'
export { default as WorkflowDatabaseSubmit } from './src/components/modal1/WorkflowDatabaseSubmit.vue'
export { default as ModalContentWrapper } from './src/components/modal1/ModalContentWrapper.vue'

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
