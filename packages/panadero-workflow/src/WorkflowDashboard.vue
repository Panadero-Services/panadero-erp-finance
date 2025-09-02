<!--
  Financial ERP Workflow Dashboard
  @version 1.1.1
  @date 26-Aug-2025
  @description Modular dashboard with separated concerns - no wrapper layers
-->
<script setup>
import { onMounted, computed } from 'vue'
import { useWorkflowDashboard } from './composables/useWorkflowDashboard.js'
// REMOVE: import { useWorkflowStore } from './composables/workflowStore.js'
import ActiveWorkflowsList from './components/ActiveWorkflowsList.vue'
import WorkflowTemplatesList from './components/WorkflowTemplatesList.vue'
import LoadingState from './components/LoadingState.vue'
import WorkflowStartOverlay from './components/modal1/WorkflowStartOverlay.vue'
import WorkflowModal from './components/modal1/ModalWrapper.vue'

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

// Composables
// REMOVE: const workflowStore = useWorkflowStore() - use props.workflowStore instead

// Dashboard state and methods
const {
  // State
  selectedWorkflow,
  showOverlay,
  activeWorkflows,
  selectedActiveWorkflow,
  showWorkflowModal,
  configWorkflows,
  loadingConfigWorkflows,
  
  // Computed
  workflowModules,
  allWorkflows,

  // Methods
  openWorkflowOverlay,
  closeOverlay,
  openWorkflowModal,
  closeWorkflowModal,
  startWorkflowDirectly,
  deleteActiveWorkflow,
  getModuleDisplayName
} = useWorkflowDashboard()

// Store state for persistence - updated to use props.workflowStore
const storeActiveWorkflows = computed(() => props.workflowStore.activeWorkflows)
const storeSelectedWorkflow = computed(() => props.workflowStore.activeWorkflows[0])

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
  console.debug('WorkflowDashboard mounted',  props.scaling)
  console.debug('Store object:', props.workflowStore)
  console.debug('Available templates:', props.workflowStore.builtInTemplates)
})
</script>

<template>
  <div class="">
    <!-- Active Workflows Section -->
    <ActiveWorkflowsList 
      v-if="activeWorkflows.length > 0"
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

    <!-- Workflow Start Overlay -->
    <WorkflowStartOverlay 
      :show="showOverlay"
      :selected-workflow="selectedWorkflow"
      :workflow-store="workflowStore"
      @close="closeOverlay"
      @start-workflow="handleStartWorkflow"
    />

    <!-- Workflow Modal -->
    <WorkflowModal 
      v-if="showWorkflowModal"
      :show="showWorkflowModal"
      :active-workflow="selectedActiveWorkflow"
      :workflow-store="props.workflowStore"
      :scaling="scaling"
      @close="closeWorkflowModal"
    />
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>