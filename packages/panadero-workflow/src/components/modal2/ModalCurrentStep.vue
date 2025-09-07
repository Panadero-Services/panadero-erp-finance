<!--
  Modal Current Step Component
  @version 1.3.0
  @date 04-Sep-2025
  @description Displays the current step of a workflow in a modal - DRY refactored
-->
<script setup>
import { computed, ref, onErrorCaptured } from 'vue'
import WorkflowEntitySelection from './WorkflowEntitySelection.vue'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowChecklist from './WorkflowChecklist.vue'
import WorkflowApproval from './WorkflowApproval.vue'

// Props
const props = defineProps({
  workflowStore: {
    type: Object,
    required: true
  },
  workflowId: {
    type: [String, Number],
    required: true
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

// Add error tracking
const errorInfo = ref(null)

// Capture any errors in child components
onErrorCaptured((error, instance, info) => {
  console.error('Error captured in ModalCurrentStep:', {
    error: error.message,
    stack: error.stack,
    component: instance.$options.name || instance.$options.__name,
    info: info
  })
  
  errorInfo.value = {
    message: error.message,
    component: instance.$options.name || instance.$options.__name,
    info: info
  }
  
  return false // Prevent error from propagating
})

// Get activeWorkflow from store reactively (SSOT)
const activeWorkflow = computed(() => {
  return props.workflowStore.workflows.find(w => w.id === props.workflowId) || null
})

// Add props logging to see what data we're receiving
console.debug('ModalCurrentStep received props:', {
  workflowId: props.workflowId,
  viewedStep: props.viewedStep,
  scaling: props.scaling,
  activeWorkflow: activeWorkflow.value
})

// Debug toggle state
const showDebug = ref(false)

// FIX: Add null checks to computed properties
const currentStepData = computed(() => {
  if (!activeWorkflow.value) return {}
  return activeWorkflow.value.steps?.[activeWorkflow.value.currentStep - 1] || {}
})

const viewedStepData = computed(() => {
  if (!activeWorkflow.value) return {}
  return activeWorkflow.value.steps?.[props.viewedStep - 1] || {}
})

// FIX: Add schema error detection
const hasSchemaError = computed(() => {
  if (!activeWorkflow.value) return true // No workflow = error
  
  // Check if entityDataStructure exists and has no error
  const entityData = activeWorkflow.value.entityDataStructure
  if (!entityData) return true // No entity data = schema error
  if (entityData.error) return true // Explicit error = schema error
  
  return false // Schema is good
})

// FIX: Only allow normal operation if schema is good
const canShowNormalInterface = computed(() => {
  return activeWorkflow.value && !hasSchemaError.value
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

// Determine display mode - only when schema is good
const isEditableMode = computed(() => {
  if (!canShowNormalInterface.value) return false
  return props.viewedStep === activeWorkflow.value.currentStep && viewedStepData.value.status !== 'completed'
})

const isReadOnlyMode = computed(() => {
  if (!canShowNormalInterface.value) return false
  return props.viewedStep < activeWorkflow.value.currentStep || viewedStepData.value.status === 'completed'
})

const isPreviewMode = computed(() => {
  if (!canShowNormalInterface.value) return false
  return props.viewedStep > activeWorkflow.value.currentStep
})
</script>

<template>
  <div class="h-full">
    <!-- ERROR DISPLAY: Show any captured errors -->
    <div v-if="errorInfo" class="p-6 text-center bg-red-50 dark:bg-red-900/20">
      <h5 class="font-medium mb-2 text-red-700 dark:text-red-300">
        Component Error Detected
      </h5>
      <p class="text-red-600 dark:text-red-400 text-sm mb-2">
        {{ errorInfo.message }}
      </p>
      <p class="text-red-500 dark:text-red-500 text-xs">
        Component: {{ errorInfo.component }} | Info: {{ errorInfo.info }}
      </p>
    </div>

    <!-- ERROR STATE: No workflow or schema error -->
    <div v-if="!canShowNormalInterface" class="p-6 text-center">
      <div class="text-red-500 dark:text-red-400">
        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
        
        <!-- No workflow found -->
        <div v-if="!activeWorkflow">
          <h5 :style="{ fontSize: scaling.font.body }" class="font-medium mb-2">
            Workflow Not Found
          </h5>
          <p :style="{ fontSize: scaling.font.small }" class="text-gray-600 dark:text-gray-300">
            The workflow could not be loaded.
          </p>
        </div>
        
        <!-- Schema error -->
        <div v-else>
          <h5 :style="{ fontSize: scaling.font.body }" class="font-medium mb-2">
            Schema Error
          </h5>
          <p :style="{ fontSize: scaling.font.small }" class="text-gray-600 dark:text-gray-300 mb-4">
            Failed to load the database schema for this workflow.
          </p>
          
          <!-- Schema error details -->
          <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-left">
            <p :style="{ fontSize: scaling.font.caption }" class="text-red-700 dark:text-red-300">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>Entity:</strong> {{ activeWorkflow.template?.entity || 'Unknown' }}
            </p>
            <p v-if="activeWorkflow.entityDataStructure?.error" :style="{ fontSize: scaling.font.caption }" class="text-red-700 dark:text-red-300 mt-1">
              <strong>Error:</strong> {{ activeWorkflow.entityDataStructure.error }}
            </p>
            <p :style="{ fontSize: scaling.font.caption }" class="text-red-700 dark:text-red-300 mt-1">
              <strong>API Endpoint:</strong> /api/entities/{{ activeWorkflow.template?.entity }}/schema
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- NORMAL INTERFACE: Only when schema is good -->
    <div v-else>

      <!-- Header with Debug Toggle -->
      <div class="mb-4">
        <div class="flex items-center justify-between">

          <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mt-2">
            Step {{viewedStep}} Input 
          </h3>

          <button 
            @click="showDebug = !showDebug"
            :style="{ fontSize: scaling.font.caption }"
            class="px-3 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
          >
            {{ showDebug ? 'Hide' : 'Show' }} Debug
          </button>
        </div>
      </div>


      <!-- Add null check wrapper around the entire content -->
      <div v-if="activeWorkflow">
        <!-- Complete WorkflowStore Debug (Collapsible) -->
        <div v-if="showDebug" class="mb-4">
          <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
            Complete WorkflowStore Debug
          </h3>
          
          <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg space-y-4">
            
            <!-- Store Settings -->
            <div>
              <h4 class="font-medium text-blue-600 dark:text-blue-400 mb-2">Settings</h4>
              <div class="ml-4 text-sm space-y-1">
                <div>Font Size: {{ workflowStore.settings.fontSize }}</div>
                <div>Dark Mode: {{ workflowStore.settings.darkMode }}</div>
                <div>Compact Layout: {{ workflowStore.settings.compactLayout }}</div>
                <div>Show Debug: {{ workflowStore.settings.showDebug }}</div>
                <div>Rows Per Page: {{ workflowStore.settings.rowsPerPage }}</div>
                <div>Auto Refresh: {{ workflowStore.settings.autoRefreshInterval }}s</div>
              </div>
            </div>

            <!-- Workflow Templates -->
            <div>
              <h4 class="font-medium text-green-600 dark:text-green-400 mb-2">Built-in Templates ({{ workflowStore.builtInTemplates.length }})</h4>
              <div class="ml-4 text-sm space-y-1 max-h-32 overflow-y-auto">
                <div v-for="template in workflowStore.builtInTemplates" :key="template.id" class="flex justify-between">
                  <span>{{ template.name }}</span>
                  <span class="text-gray-600 dark:text-gray-400">{{ template.id }}</span>
                </div>
              </div>
            </div>

            <!-- All Workflows -->
            <div>
              <h4 class="font-medium text-purple-600 dark:text-purple-400 mb-2">All Workflows ({{ workflowStore.workflows.length }})</h4>
              <div class="ml-4 text-sm space-y-2">
                <div v-for="workflow in workflowStore.workflows" :key="workflow.id" class="border border-gray-300 dark:border-gray-600 p-2 rounded">
                  <div class="font-medium">{{ workflow.name }} (ID: {{ workflow.id }})</div>
                  <div class="text-gray-600 dark:text-gray-400">
                    Status: {{ workflow.status }} | 
                    Step: {{ workflow.currentStep }}/{{ workflow.steps?.length || 0 }} | 
                    Module: {{ workflow.module }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-500">
                    Created: {{ new Date(workflow.created_at).toLocaleString() }}
                  </div>
                  <div v-if="workflow.entityDataStructure" class="text-xs">
                    <div class="font-medium mt-1">Entity Data Structure:</div>
                    <div>Entity: {{ workflow.entityDataStructure.entity }}</div>
                    <div>Fields: {{ workflow.entityDataStructure.fields?.length || 0 }}</div>
                    <div v-if="workflow.entityDataStructure.error" class="text-red-500">
                      Error: {{ workflow.entityDataStructure.error }}
                    </div>
                  </div>
                  <div v-if="workflow.steps && workflow.steps.length > 0" class="text-xs mt-1">
                    <div class="font-medium">Steps:</div>
                    <div v-for="(step, index) in workflow.steps" :key="step.id" class="ml-2">
                      {{ index + 1 }}. {{ step.name }} ({{ step.type }}) - {{ step.status }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Computed Properties -->
            <div>
              <h4 class="font-medium text-orange-600 dark:text-orange-400 mb-2">Computed Properties</h4>
              <div class="ml-4 text-sm space-y-1">
                <div>Active Workflows: {{ workflowStore.activeWorkflows.length }}</div>
                <div>Pending Approvals: {{ workflowStore.pendingApprovals.length }}</div>
                <div>Completed Workflows: {{ workflowStore.completedWorkflows.length }}</div>
                <div>Draft Workflows: {{ workflowStore.draftWorkflows.length }}</div>
              </div>
            </div>

            <!-- Current Workflow Details -->
            <div>
              <h4 class="font-medium text-red-600 dark:text-red-400 mb-2">Current Workflow Details</h4>
              <div class="ml-4 text-sm space-y-1">
                <div>ID: {{ activeWorkflow.id }}</div>
                <div>Name: {{ activeWorkflow.name }}</div>
                <div>Description: {{ activeWorkflow.description }}</div>
                <div>Status: {{ activeWorkflow.status }}</div>
                <div class="font-bold text-blue-600 dark:text-blue-400">
                  Current Step: {{ activeWorkflow?.currentStep || 'N/A' }}
                </div>
                <div>Viewed Step: {{ props.viewedStep }}</div>
                <div>Module: {{ activeWorkflow.module }}</div>
                <div>Template ID: {{ activeWorkflow.template_id }}</div>
                <div>Created By: {{ activeWorkflow.created_by }}</div>
                <div>Workflow Nr: {{ activeWorkflow.workflowNr }}</div>
                <div>Started At: {{ activeWorkflow.started_at ? new Date(activeWorkflow.started_at).toLocaleString() : 'Not started' }}</div>
                <div>Completed At: {{ activeWorkflow.completed_at ? new Date(activeWorkflow.completed_at).toLocaleString() : 'Not completed' }}</div>
              </div>
            </div>

            <!-- Current Step Data -->
            <div v-if="viewedStepData">
              <h4 class="font-medium text-indigo-600 dark:text-indigo-400 mb-2">Current Step Data</h4>
              <div class="ml-4 text-sm space-y-1">
                <div>Step ID: {{ viewedStepData.id }}</div>
                <div>Step Name: {{ viewedStepData.name }}</div>
                <div>Step Type: {{ viewedStepData.type }}</div>
                <div>Step Status: {{ viewedStepData.status }}</div>
                <div>Required: {{ viewedStepData.required }}</div>
                <div>Order: {{ viewedStepData.order }}</div>
                <div>Approval Required: {{ viewedStepData.approval_required }}</div>
                <div>Estimated Duration: {{ viewedStepData.estimated_duration }}</div>
                <div v-if="viewedStepData.data && Object.keys(viewedStepData.data).length > 0">
                  <div class="font-medium mt-1">Step Data:</div>
                  <div v-for="(value, key) in viewedStepData.data" :key="key" class="ml-2">
                    {{ key }}: {{ value }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Raw JSON (Collapsible) -->
            <details class="mt-4">
              <summary class="font-medium text-gray-600 dark:text-gray-400 cursor-pointer">Raw JSON (Click to expand)</summary>
              <pre class="mt-2 text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded overflow-auto max-h-64">{{ JSON.stringify(workflowStore, null, 2) }}</pre>
            </details>

          </div>
        </div>

        <!-- EDITABLE CONTENT -->
        <div v-if="isEditableMode">
          <component 
            :is="getStepComponent(viewedStepData.type)"
            v-if="getStepComponent(viewedStepData.type)"
            :step="viewedStepData"
            :step-data="viewedStepData.data || {}"
            :scaling="scaling"
            :workflow-id="props.workflowId"
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
                  Step {{ props.viewedStep }} - {{ viewedStepData.name || 'Unknown Step' }}
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
              :workflow-id="props.workflowId"
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
                Step {{ props.viewedStep }} - {{ viewedStepData.name || 'Unknown Step' }}
              </h3>
              <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">
                This step is not yet available. Complete the previous steps to unlock this step.
              </p>
              <div class="mt-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                  <i class="fas fa-info-circle mr-1"></i>
                  Current Step: {{ activeWorkflow?.currentStep || 'N/A' }}
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
              :workflow-id="props.workflowId"
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
    </div>
  </div>
</template>
