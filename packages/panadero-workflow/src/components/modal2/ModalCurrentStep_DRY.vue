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
  viewedStep: {
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

// Simple logic: current step = editable, past steps = read-only completed, future steps = locked preview

// Get the viewed step data (for display only)
const viewedStepData = computed(() => {
  return props.workflow.steps?.[props.viewedStep] || {}
})

// Dynamic font sizes - ONLY changing styling references
const _h3 = computed(() => props.scaling.font.subtitle) // WAS: `${settings.fontSizesComputed.value.h3}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`

// DRY: Get the appropriate step component based on step type
const getStepComponent = (stepData) => {
  switch (stepData.type) {
    case 'shared_entity_selection':
      return 'WorkflowEntitySelection'
    case 'form_submission':
      return 'WorkflowForm'
    case 'checklist':
      return 'WorkflowChecklist'
    case 'approval':
      return 'WorkflowApproval'
    default:
      return null
  }
}

// DRY: Get step component props
const getStepProps = (stepData, isReadOnly = false) => {
  const baseProps = {
    step: stepData,
    'step-data': stepData.data || {},
    scaling: props.scaling,
    'active-workflow': props.workflow
  }

  // Add read-only props based on component type
  if (isReadOnly) {
    if (stepData.type === 'form_submission') {
      baseProps['read-only-preview'] = true
    } else {
      baseProps['info-only'] = true
    }
  }

  return baseProps
}

// DRY: Get step component events
const getStepEvents = (isReadOnly = false) => {
  if (isReadOnly) {
    return {} // No events for read-only mode
  }

  return {
    'update-step-data': (stepId, stepData) => {
      const handlerMap = {
        'shared_entity_selection': handleEntitySelected,
        'form_submission': handleFormDataUpdated,
        'checklist': handleChecklistUpdated,
        'approval': handleApprovalUpdated
      }
      const handler = handlerMap[viewedStepData.value.type]
      if (handler) handler(stepId, stepData)
    },
    'step-completed': () => emit('step-completed')
  }
}

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

// Handle entity selection - just save data, don't complete step
const handleEntitySelected = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
}

// Handle form data updates - just save data, don't complete step
const handleFormDataUpdated = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
}

// Handle checklist updates - just save data, don't complete step
const handleChecklistUpdated = (stepId, stepData) => {
  emit('step-data-updated', {
    stepIndex: props.currentStep,
    data: stepData
  })
}

// Handle approval updates - just save data, don't complete step
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
    <!-- Step Header -->
    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
      <h2 :style="{ fontSize: scaling.font.title }" class="font-semibold text-gray-900 dark:text-white">
        Step {{ viewedStep + 1 }} - {{ viewedStepData.name || 'Unknown Step' }}
      </h2>
      <p v-if="viewedStepData.description" :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300 mt-1">
        {{ viewedStepData.description }}
      </p>
    </div>
    
    <!-- EDITABLE CONTENT - Current step only if not completed -->
    <div v-if="viewedStep === currentStep && viewedStepData.status !== 'completed'">
      <component
        :is="getStepComponent(viewedStepData)"
        v-bind="getStepProps(viewedStepData, false)"
        v-on="getStepEvents(false)"
      />
    </div>
    
    <!-- READ-ONLY COMPLETED - Past steps OR completed current step -->
    <div v-else-if="viewedStep < currentStep || viewedStepData.status === 'completed'">
      <!-- Completed Step Header -->
      <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg mb-4">
        <div class="flex items-center">
          <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3"></i>
          <div>
            <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-medium text-green-800 dark:text-green-200">
              Step {{ viewedStep + 1 }} - {{ viewedStepData.name || 'Unknown Step' }}
            </h3>
            <p :style="{ fontSize: scaling.font.caption }" class="text-green-700 dark:text-green-300">
              This step has been completed
            </p>
          </div>
        </div>
      </div>
      
      <!-- Read-only step content -->
      <div class="opacity-75 pointer-events-none">
        <component
          :is="getStepComponent(viewedStepData)"
          v-bind="getStepProps(viewedStepData, true)"
          v-on="getStepEvents(true)"
        />
      </div>
    </div>

    <!-- LOCKED PREVIEW - Future steps -->
    <div v-else>
      <!-- Lock Screen -->
      <div class="p-6 text-center">
        <div class="text-gray-500 dark:text-gray-400">
          <i class="fas fa-lock text-4xl mb-4"></i>
          <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-medium mb-2">
            Step {{ viewedStep + 1 }} - {{ viewedStepData.name || 'Unknown Step' }}
          </h3>
          <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">
            This step is not yet available. Complete the previous steps to unlock this step.
          </p>
          <div class="mt-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
              <i class="fas fa-info-circle mr-1"></i>
              Current Step: {{ currentStep + 1 }}
            </span>
          </div>
        </div>
      </div>
      
      <!-- Read-only step content preview -->
      <div class="opacity-60 pointer-events-none">
        <component
          :is="getStepComponent(viewedStepData)"
          v-bind="getStepProps(viewedStepData, true)"
          v-on="getStepEvents(true)"
        />
      </div>
    </div>
    
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
