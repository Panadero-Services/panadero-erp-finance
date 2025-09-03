<!--
  Modal Current Step Component
  @version 1.2.0
  @description Displays the current step of a workflow in a modal
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import WorkflowEntitySelection from './WorkflowEntitySelection.vue'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowChecklist from './WorkflowChecklist.vue'
import WorkflowApproval from './WorkflowApproval.vue'

// Props
const props = defineProps({
  workflow: {
    type: Object,
    required: true
  },
  workflowStore: {
    type: Object,
    required: true
  },
  currentStep: {
    type: Number,
    default: 0
  },
  scaling: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['step-data-updated', 'step-completed'])

// Settings
//const settings = useWorkflowSettings()

// Computed properties
const currentStepData = computed(() => {
  return props.workflow.steps?.[props.currentStep] || {}
})

const stepProgress = computed(() => {
  const totalSteps = props.workflow.steps?.length || 0
  return totalSteps > 0 ? ((props.currentStep + 1) / totalSteps) * 100 : 0
})

const stepStatus = computed(() => {
  return currentStepData.value.status || 'pending'
})

// Dynamic font sizes - ONLY changing styling references
const _h3 = computed(() => props.scaling.font.subtitle) // WAS: `${settings.fontSizesComputed.value.h3}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`

// Get color classes from store
const getColorClasses = (colorType, element) => {
  return props.workflowStore.getColorClasses(colorType, element)
}

// Get status color
const getStatusColor = (status) => {
  switch (status) {
    case 'completed':
      return 'text-green-600 dark:text-green-400'
    case 'active':
      return 'text-blue-600 dark:text-blue-400'
    case 'requires_approval':
      return 'text-yellow-600 dark:text-yellow-400'
    case 'failed':
      return 'text-red-600 dark:text-red-400'
    default:
      return 'text-gray-600 dark:text-gray-400'
  }
}

// Check step types
const needsEntitySelection = computed(() => {
  return currentStepData.value.type === 'shared_entity_selection'
})

const needsFormSubmission = computed(() => {
  return currentStepData.value.type === 'form_submission'
})

const needsChecklist = computed(() => {
  return currentStepData.value.type === 'checklist'
})

const needsApproval = computed(() => {
  return currentStepData.value.type === 'approval'
})

// Handle entity selection completion
const handleEntitySelected = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
  
  // Complete the step in the store
  try {
    props.workflowStore.completeStep(
      props.workflow.instanceId || props.workflow.id,
      props.currentStep,
      stepData,
      'current_user'
    )
    console.debug('Step completed successfully')
  } catch (error) {
    console.error('Failed to complete step:', error)
  }
}

// Handle form data updates  
const handleFormDataUpdated = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
  
  // Complete the step in the store
  try {
    props.workflowStore.completeStep(
      props.workflow.instanceId || props.workflow.id,
      props.currentStep,
      stepData,
      'current_user'
    )
    console.debug('Form step completed successfully')
  } catch (error) {
    console.error('Failed to complete form step:', error)
  }
}

// Handle checklist updates
const handleChecklistUpdated = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
  
  // Complete the step in the store if all required items are checked
  try {
    props.workflowStore.completeStep(
      props.workflow.instanceId || props.workflow.id,
      props.currentStep,
      stepData,
      'current_user'
    )
    console.debug('Checklist step completed successfully')
  } catch (error) {
    console.error('Failed to complete checklist step:', error)
  }
}

// Handle approval updates
const handleApprovalUpdated = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
  
  console.debug('Approval data updated:', {
    stepId,
    stepData,
    currentStep: props.currentStep
  })
}

// Debug the props
console.debug('ModalCurrentStep props:', props)
console.debug('ModalCurrentStep workflow:', props.workflow)
console.debug('ModalCurrentStep workflowStore:', props.workflowStore)

</script>

<template>
  <div class="space-y-6 bg-gray-50 dark:bg-gray-800">
    <!-- NO STEP HEADER - Information shown in modal header instead -->
    
    <!-- Entity Selection for vendor selection steps -->
    <WorkflowEntitySelection 
      v-if="needsEntitySelection"
      :step="currentStepData"
      :step-data="currentStepData.data || {}"
      :scaling="scaling"
      @update-step-data="handleEntitySelected"
      @step-completed="$emit('step-completed')"
    />
    
    <!-- Form Submission for financial information steps -->
    <WorkflowForm 
      v-if="needsFormSubmission"
      :step="currentStepData"
      :step-data="currentStepData.data || {}"
      :scaling="scaling"
      @update-step-data="handleFormDataUpdated"
      @step-completed="$emit('step-completed')"
    />
    
    <!-- Checklist for due diligence steps -->
    <WorkflowChecklist 
      v-if="needsChecklist"
      :step="currentStepData"
      :step-data="currentStepData.data || {}"
      :scaling="scaling"
      @update-step-data="handleChecklistUpdated"
      @step-completed="$emit('step-completed')"
    />
    
    <!-- Approval for contract negotiation and final approval steps -->
    <WorkflowApproval 
      v-if="needsApproval"
      :step="currentStepData"
      :step-data="currentStepData.data || {}"
      :scaling="scaling"
      @update-step-data="handleApprovalUpdated"
      @step-completed="$emit('step-completed')"
    />
    
    <!-- NO STEP DESCRIPTION - Shown in modal header instead -->
    
    <!-- Step Data Display -->
    <div v-if="currentStepData.data && Object.keys(currentStepData.data).length > 0" class="space-y-3 border rounded-lg p-3 bg-gray-100 dark:bg-gray-800" >
      <h4 :style="{ fontSize: _body }" class="font-medium text-gray-700 dark:text-gray-300">
        Step Data
      </h4>
      <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 space-y-2">
        <div 
          v-for="(value, key) in currentStepData.data" 
          :key="key"
          class="flex justify-between">
          <span :style="{ fontSize: _caption }" class="font-medium text-gray-600 dark:text-gray-400">
            {{ key }}:
          </span>
          <span :style="{ fontSize: _caption }" class="text-gray-900 dark:text-white">
            {{ value }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Step History -->
    <div v-if="currentStepData.history && currentStepData.history.length > 0" class="space-y-3 bg-gray-100 dark:bg-gray-800 p-3">
      <h4 :style="{ fontSize: _body }" class="font-medium text-gray-700 dark:text-gray-300">
        Step History
      </h4>
      <div class="space-y-2">
        <div 
          v-for="(entry, index) in currentStepData.history" 
          :key="index"
          class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
          <div class="flex justify-between items-start mb-1">
            <span :style="{ fontSize: _caption }" class="font-medium text-gray-600 dark:text-gray-400">
              {{ entry.action }}
            </span>
            <span :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">
              {{ new Date(entry.timestamp).toLocaleString() }}
            </span>
          </div>
          <p :style="{ fontSize: _caption }" class="text-gray-700 dark:text-gray-300">
            {{ entry.details }}
          </p>
          <p v-if="entry.user" :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400 mt-1">
            By: {{ entry.user }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>