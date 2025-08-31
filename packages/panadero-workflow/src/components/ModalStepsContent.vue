<!--
  Modal Steps Content Component
  @version 1.1.4
  @description Steps tab content for modal with simplified store usage
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import WorkflowStepper from './WorkflowStepper.vue'

// Props
const props = defineProps({
  workflowSteps: {
    type: Array,
    default: () => []
  },
  activeWorkflow: {
    type: Object,
    default: null
  },
  workflowStore: {
    type: Object,
    required: true
  }
})

// Composables
const settings = useWorkflowSettings()

// Dynamic font sizes
const _value = computed(() => `${settings.fontSizesComputed.value.h4}px`)

// Computed
const currentStepIndex = computed(() => {
  // FIXED: Add null check before accessing currentStep
  return props.activeWorkflow?.currentStep || 0
})

// Methods
function handleStepClick(stepIndex) {
  console.debug('Step clicked:', stepIndex)
  
  // Update the workflow instance's current step directly
  if (props.activeWorkflow) {
    // Update the activeWorkflow object
    props.activeWorkflow.currentStep = stepIndex
    
    // Also update in the store if it exists
    if (props.workflowStore && props.activeWorkflow.instanceId) {
      const workflow = props.workflowStore.workflows.find(w => w.id === props.activeWorkflow.instanceId)
      if (workflow) {
        workflow.currentStep = stepIndex
      }
    }
    
    console.debug('Updated current step to:', stepIndex)
  }
}
</script>

<template>
  <div class="p-2 sm:p-4">
    <WorkflowStepper 
      v-if="workflowSteps.length > 0"
      :steps="workflowSteps"
      :currentStep="currentStepIndex"
      @step-click="handleStepClick"
    />
    <div v-else class="text-center py-16">
      <i class="fas fa-list-ol text-gray-400 text-3xl mb-4"></i>
      <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
        No workflow steps available
      </p>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
