<!--
  Financial ERP Workflow Dashboard
  @version 1.1.3
  @date 04-Sep-2025
  @description Fixed modal state management
-->
<script setup>
import { onMounted, computed } from 'vue'
import { useWorkflowDashboard } from './composables/useWorkflowDashboard.js'
import ActiveWorkflowsList from './components/ActiveWorkflowsList.vue'
import WorkflowTemplatesList from './components/WorkflowTemplatesList.vue'
import LoadingState from './components/LoadingState.vue'
import ModalWrapper from './components/modal2/ModalWrapper.vue'

// Props - RECEIVE the store from parent
const props = defineProps({
  workflowStore: {
    type: Object,
    required: true
  },
  scaling: {
    type: Object,
    default: true
  }
})

// Emits
const emit = defineEmits(['workflow-selected'])

// Dashboard state and methods
const {
  // State
  activeWorkflows,
  selectedActiveWorkflow,
  showWorkflowModal,
  configWorkflows,
  loadingConfigWorkflows,
  
  // Computed
  workflowModules,
  allWorkflows,

  // Methods
  openWorkflowModal,
  closeWorkflowModal,
  startWorkflowDirectly,
  deleteActiveWorkflow,
  getModuleDisplayName
} = useWorkflowDashboard()

// Handle workflow start events
async function handleStartWorkflow(workflow) {
  try {
    await startWorkflowDirectly(workflow)
  } catch (error) {
    console.error('Failed to start workflow:', error)
  }
}

// Lifecycle
onMounted(() => {
  console.debug('WorkflowDashboard mounted', props.scaling)
  console.debug('Store object:', props.workflowStore)
  console.debug('Available templates:', props.workflowStore.builtInTemplates)
})
</script>

<template>
  <div class="space-y-6">
    <!-- Active Workflows List -->
    <ActiveWorkflowsList 
      :active-workflows="activeWorkflows"
      :workflow-store="workflowStore"
      :scaling="scaling"
      @open-modal="openWorkflowModal"
      @delete-workflow="deleteActiveWorkflow"
    />

    <!-- Loading State -->
    <LoadingState 
      v-if="loadingConfigWorkflows || (!workflowModules.length)"
      :loading-config-workflows="loadingConfigWorkflows"
      :config-workflows="configWorkflows"
      :workflow-modules="workflowModules"
      :workflow-store="workflowStore"
      :scaling="scaling"
    />

    <!-- Workflow Templates List -->
    <WorkflowTemplatesList 
      v-else
      :all-workflows="allWorkflows"
      :workflow-store="workflowStore"
      :scaling="scaling"
      @start-workflow="handleStartWorkflow"
    />

    <!-- Workflow ModalWrapper -->
    <ModalWrapper 
      v-if="showWorkflowModal"
      :show="showWorkflowModal"
          :active-workflow="selectedActiveWorkflow"
      :workflow-id="selectedActiveWorkflow?.id"
      :workflow-store="workflowStore"
      :scaling="scaling"
      @close="closeWorkflowModal"
    />
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>