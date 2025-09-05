<!--
  Modal Current Step Component
  @version 1.3.0
  @date 04-Sep-2025
  @description Displays the current step of a workflow in a modal - DRY refactored
-->
<script setup>
import { computed } from 'vue'
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

// NO EMITS NEEDED - components call store directly!

// Computed properties
const currentStepData = computed(() => {
  return props.workflow.steps?.[props.currentStep] || {}
})

const viewedStepData = computed(() => {
  return props.workflow.steps?.[props.viewedStep] || {}
})

// Dynamic component mapping
const stepComponents = {
  'shared_entity_selection': WorkflowEntitySelection,
  'form_submission': WorkflowForm,
  'checklist': WorkflowChecklist,
  'approval': WorkflowApproval
}

// Get the appropriate component for the step type
const getStepComponent = (stepType) => {
  return stepComponents[stepType] || null
}

// Determine display mode
const isEditableMode = computed(() => {
  return props.viewedStep === props.currentStep && viewedStepData.value.status !== 'completed'
})

const isReadOnlyMode = computed(() => {
  return props.viewedStep < props.currentStep || viewedStepData.value.status === 'completed'
})

const isPreviewMode = computed(() => {
  return props.viewedStep > props.currentStep
})
</script>

<template>
  <div class="flex-1 overflow-y-auto p-2 mt-0">
    <!-- Step Header 
    <div class="mb-4">
      <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
        Input Section
      </h3>
    </div>-->

    <div class="mb-4">
      <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
       {{workflowStore.settings}}
      </h3>
    </div>


    <!-- EDITABLE CONTENT -->
    <div v-if="isEditableMode">
      <component 
        :is="getStepComponent(viewedStepData.type)"
        v-if="getStepComponent(viewedStepData.type)"
        :step="viewedStepData"
        :step-data="viewedStepData.data || {}"
        :scaling="scaling"
        :active-workflow="workflow"
        :workflow-store="workflowStore"
      />
    </div>
    
    <!-- READ-ONLY COMPLETED -->
    <div v-else-if="isReadOnlyMode">
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
          :is="getStepComponent(viewedStepData.type)"
          v-if="getStepComponent(viewedStepData.type)"
          :step="viewedStepData"
          :step-data="viewedStepData.data || {}"
          :scaling="scaling"
          :active-workflow="workflow"
          :workflow-store="workflowStore"
          :info-only="true"
        />
      </div>
    </div>

    <!-- LOCKED PREVIEW -->
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
          :is="getStepComponent(viewedStepData.type)"
          v-if="getStepComponent(viewedStepData.type)"
          :step="viewedStepData"
          :step-data="viewedStepData.data || {}"
          :scaling="scaling"
          :active-workflow="workflow"
          :workflow-store="workflowStore"
          :info-only="true"
        />
      </div>
    </div>
    
    <!-- Step Data Display -->
    <div v-if="currentStepData.data && Object.keys(currentStepData.data).length > 0" class="space-y-3 border rounded-lg p-3 bg-gray-100 dark:bg-gray-800 mt-4">
      <h4 :style="{ fontSize: scaling.font.body }" class="font-medium text-gray-700 dark:text-gray-300">
        Step Data
      </h4>
      <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 space-y-2">
        <div 
          v-for="(value, key) in currentStepData.data" 
          :key="key"
          class="flex justify-between">
          <span :style="{ fontSize: scaling.font.caption }" class="font-medium text-gray-600 dark:text-gray-400">
            {{ key }}:
          </span>
          <span :style="{ fontSize: scaling.font.caption }" class="text-gray-900 dark:text-white">
            {{ value }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Step History -->
    <div v-if="currentStepData.history && currentStepData.history.length > 0" class="space-y-3 bg-gray-100 dark:bg-gray-800 p-3 mt-4">
      <h4 :style="{ fontSize: scaling.font.body }" class="font-medium text-gray-700 dark:text-gray-300">
        Step History
      </h4>
      <div class="space-y-2">
        <div 
          v-for="(entry, index) in currentStepData.history" 
          :key="index"
          class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
          <div class="flex justify-between items-start mb-1">
            <span :style="{ fontSize: scaling.font.caption }" class="font-medium text-gray-600 dark:text-gray-400">
              {{ entry.action }}
            </span>
            <span :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
              {{ new Date(entry.timestamp).toLocaleString() }}
            </span>
          </div>
          <p :style="{ fontSize: scaling.font.caption }" class="text-gray-700 dark:text-gray-300">
            {{ entry.details }}
          </p>
          <p v-if="entry.user" :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400 mt-1">
            By: {{ entry.user }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
