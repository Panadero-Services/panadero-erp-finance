<!--
  Workflow Step Info Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Step information component - displays template information with dynamic styling
-->
<script setup>
import { computed } from 'vue'
import { useColors } from './useColors.js'

// Color system
const { colors } = useColors()
// Props
const props = defineProps({
  workflowSteps: {
    type: Array,
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
  },
  activeWorkflow: {
    type: Object,
    default: null
  }
})

// Get form field count
function getFormFieldCount(step) {
  if (!step.form_schema?.sections) return 0
  let count = 0
  step.form_schema.sections.forEach(section => {
    count += section.fields?.length || 0
  })
  return count
}

// Get step icon based on type
function getStepIcon(stepType) {
  switch (stepType) {
    case 'shared_entity_selection':
      return 'fas fa-database text-purple-600'
    case 'form_submission':
      return 'fas fa-edit text-green-600'
    case 'approval':
      return 'fas fa-user-check text-blue-600'
    case 'checklist':
      return 'fas fa-list-check text-orange-600'
    default:
      return 'fas fa-info-circle text-gray-600'
  }
}

// Get step status text - Only 3 statuses: pending, active, completed
function getStepStatusText(step) {
  if (!step) return 'Unknown'
  
  // Only use the 3 main statuses
  if (step.status === 'completed') return 'Completed'
  if (step.status === 'active') return 'Active'
  if (step.status === 'pending') return 'Pending'
  
  // Default fallback
  return 'Pending'
}

// Get step status CSS class - Only 3 statuses: pending, active, completed
function getStepStatusClass(step) {
  if (!step) return 'text-gray-500'
  
  // Only use the 3 main statuses
  if (step.status === 'completed') return 'text-green-600'
  if (step.status === 'active') return 'text-blue-600'
  if (step.status === 'pending') return 'text-gray-500'
  
  // Default fallback
  return 'text-gray-500'
}

// Helper functions for status checks
function getCompletedFormFields(step) {
  if (!step.form_schema?.sections || !step.data?.formData) return 0
  let completed = 0
  step.form_schema.sections.forEach(section => {
    section.fields.forEach(field => {
      if (step.data.formData[field.name]) completed++
    })
  })
  return completed
}

function getApprovedCount(step) {
  if (!step.approvers?.length || !step.data?.approvals) return 0
  return step.data.approvals.filter(approval => approval.approved).length
}

function getCompletedChecklistItems(step) {
  if (!step.form_schema?.sections) return 0
  let totalFields = 0
  let completedFields = 0
  
  step.form_schema.sections.forEach(section => {
    section.fields.forEach(field => {
      if (field.type === 'checkbox') {
        totalFields++
        if (step.data?.[field.name]) {
          completedFields++
        }
      }
    })
  })
  
  return completedFields
}

function getChecklistProgress(step) {
  if (!step.form_schema?.sections) return 0
  let totalFields = 0
  let completedFields = 0
  
  step.form_schema.sections.forEach(section => {
    section.fields.forEach(field => {
      if (field.type === 'checkbox') {
        totalFields++
        if (step.data?.[field.name]) {
          completedFields++
        }
      }
    })
  })
  
  return totalFields > 0 ? Math.round((completedFields / totalFields) * 100) : 0
}

function getTotalChecklistItems(step) {
  if (!step.form_schema?.sections) return 0
  let totalFields = 0
  
  step.form_schema.sections.forEach(section => {
    section.fields.forEach(field => {
      if (field.type === 'checkbox') {
        totalFields++
      }
    })
  })
  
  return totalFields
}

function getFormProgress(step) {
  if (!step.form_schema?.sections) return 0
  const totalFields = getFormFieldCount(step)
  const completedFields = getCompletedFormFields(step)
  return totalFields > 0 ? Math.round((completedFields / totalFields) * 100) : 0
}

// Helper functions for step data display
function hasStepData(step) {
  if (!step?.data) return false
  
  return !!(
    step.data.formData ||
    step.data.selectedEntity ||
    step.data.approved !== undefined ||
    step.data.comments ||
    hasOtherStepData(step)
  )
}

function hasOtherStepData(step) {
  if (!step?.data) return false
  
  const knownKeys = ['formData', 'selectedEntity', 'approved', 'comments']
  return Object.keys(step.data).some(key => !knownKeys.includes(key) && step.data[key] !== null && step.data[key] !== undefined)
}

function getOtherStepData(step) {
  if (!step?.data) return {}
  
  const knownKeys = ['formData', 'selectedEntity', 'approved', 'comments']
  const otherData = {}
  
  Object.keys(step.data).forEach(key => {
    if (!knownKeys.includes(key) && step.data[key] !== null && step.data[key] !== undefined) {
      otherData[key] = step.data[key]
    }
  })
  
  return otherData
}

// Get all collected data from all steps
function getAllCollectedData() {
  if (!props.activeWorkflow?.steps) return null
  
  const allData = {}
  
  // Collect data from all steps
  props.activeWorkflow.steps.forEach((step, index) => {
    if (step.data) {
      // For selectedEntity, only keep essential info to save space
      if (step.data.selectedEntity) {
        allData.selectedEntity = {
          id: step.data.selectedEntity.id,
          name: step.data.selectedEntity.name || step.data.selectedEntity.company_name,
          type: step.data.selectedEntity.vendor_type || step.data.selectedEntity.entity_type
        }
      } else {
        Object.assign(allData, step.data)
      }
    }
  })
  
  // Add entity data structure fields (empty if not filled)
  if (props.activeWorkflow.entityDataStructure?.fields) {
    props.activeWorkflow.entityDataStructure.fields.forEach(field => {
      if (!(field.name in allData)) {
        allData[field.name] = null // Show empty fields
      }
    })
  }
  
  return Object.keys(allData).length > 0 ? allData : null
}

// Get complete table structure with all fields
function getCompleteTableStructure() {
  const allData = {}
  
  // First, collect all data from steps
  if (props.activeWorkflow?.steps) {
    props.activeWorkflow.steps.forEach((step, index) => {
      if (step.data) {
        // For selectedEntity, map it to vendor_id for entity selection steps
        if (step.data.selectedEntity && step.type === 'shared_entity_selection') {
          allData.vendor_id = step.data.selectedEntity.id
        }
        
        // Keep the selectedEntity info for display
        if (step.data.selectedEntity) {
          allData.selectedEntity = {
            id: step.data.selectedEntity.id,
            name: step.data.selectedEntity.name || step.data.selectedEntity.company_name,
            type: step.data.selectedEntity.vendor_type || step.data.selectedEntity.entity_type
          }
        }
        
        // Add all other step data
        Object.assign(allData, step.data)
      }
    })
  }
  
  // If we have entity data structure, use it to create complete structure
  if (props.activeWorkflow?.entityDataStructure?.fields) {
    const completeStructure = {}
    
    // Add all fields from entity data structure
    // FIXED: Use Object.entries instead of forEach since fields is an object
    Object.entries(props.activeWorkflow.entityDataStructure.fields).forEach(([fieldName, fieldData]) => {
      completeStructure[fieldName] = allData[fieldName] || null
    })
    
    // Add any additional data that's not in the main structure
    Object.keys(allData).forEach(key => {
      if (!(key in completeStructure)) {
        completeStructure[key] = allData[key]
      }
    })
    
    return completeStructure
  }
  
  // Fallback: if no entity structure, just return collected data
  return Object.keys(allData).length > 0 ? allData : null
}

// Format field names for display - SHOW ORIGINAL COLUMN NAMES
function formatFieldName(key) {
  return key // Just return the original field name without any conversion
}




const progressPercentage = computed(() => {
  if (!props.workflowSteps[props.viewedStep]) return -1;
  
  const step = props.workflowSteps[props.viewedStep];
  
  if (step.type === 'checklist') {
    return getChecklistProgress(step);
  }
  
  if (step.type === 'form_submission') {
    return getFormProgress(step);
  }
  
  if (step.type === 'shared_entity_selection') {
    return step.data?.selectedEntity ? 100 : 0;
  }
  
  if (step.type === 'approval') {
    if (!step.approvers?.length) return 0;
    const approvedCount = getApprovedCount(step);
    return Math.round((approvedCount / step.approvers.length) * 100);
  }
  
  return -1;
});

</script>




<template>

  <!-- Vertical Info Column 2 -->
  <div class="flex-1 overflow-y-auto p-2 mt-0">
    <!-- Stepper Header -->
    <div class="mb-4">
      <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
        Step Information
      </h3>
    </div>
    

    <!-- Header with Step Type + Colored Icon -->
    <div class="flex justify-between"  :class="['mb-3 rounded-lg px-4 py-2 border', colors.secondary.bg, colors.secondary.border]">

      <!-- Title-->
      <div v-if="workflowSteps[viewedStep]" class="flex items-center ">
        <i :class="getStepIcon(workflowSteps[viewedStep].type)" class="mr-2"></i>
        <span :style="{ fontSize: scaling.font.body }" :class="[colors.secondary.text, 'capitalize']">
          {{ workflowSteps[viewedStep].type?.replace(/_/g, ' ') || 'Unknown' }}
        </span>
      </div>

      <!-- Progress Ring -->
      <div class="relative w-8 h-8 items-end justify-end text-right">
        <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 36 36">
          <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
          <path :class="progressPercentage==100 ? 'text-green-600 dark:text-green-600'  : 'text-gray-400 dark:text-blue-600'"  stroke="currentColor" stroke-width="3" fill="none" 
                :stroke-dasharray="`${progressPercentage}, 100`"
                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
        </svg>
        <div :style="{ fontSize: scaling.font.tiny }" class="absolute inset-0 flex items-center justify-center">
          <span class="font-bold text-gray-900 dark:text-white">{{ progressPercentage }}%</span>
        </div>
      </div>

    </div>









    <!-- Template Information Body -->
    <div v-if="workflowSteps[viewedStep]" :class="['rounded-lg p-4 border', colors.secondary.bg, colors.secondary.border]">
      <div :style="{ fontSize: scaling.font.label }" :class="[colors.secondary.text, 'space-y-2']">
        <!-- Step Status -->
        <div class="flex justify-between">
          <span class="font-medium">Status:</span>
          <span :class="getStepStatusClass(workflowSteps[viewedStep])" class="font-medium">
            {{ getStepStatusText(workflowSteps[viewedStep]) }}
          </span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].entity_type" class="flex justify-between">
          <span class="font-medium">Entity Type:</span>
          <span>{{ workflowSteps[viewedStep].entity_type }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].entity_selection?.api_endpoint" class="flex justify-between">
          <span class="font-medium">API Endpoint:</span>
          <span class="font-mono">{{ workflowSteps[viewedStep].entity_selection.api_endpoint }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].entity_selection?.allow_create_new !== undefined" class="flex justify-between">
          <span class="font-medium">Allow Create New:</span>
          <span>{{ workflowSteps[viewedStep].entity_selection.allow_create_new ? 'Yes' : 'No' }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].form_schema?.sections" class="flex justify-between">
          <span class="font-medium">Total Fields:</span>
          <span>{{ getFormFieldCount(workflowSteps[viewedStep]) }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].form_schema?.sections" class="flex justify-between">
          <span class="font-medium">Sections:</span>
          <span>{{ workflowSteps[viewedStep].form_schema.sections.length }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].approvers?.length" class="flex justify-between">
          <span class="font-medium">Approvers:</span>
          <span>{{ workflowSteps[viewedStep].approvers.length }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].approval_required !== undefined" class="flex justify-between">
          <span class="font-medium">Approval Required:</span>
          <span>{{ workflowSteps[viewedStep].approval_required ? 'Yes' : 'No' }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].checklist_items?.length" class="flex justify-between">
          <span class="font-medium">Checklist Items:</span>
          <span>{{ workflowSteps[viewedStep].checklist_items.length }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].required !== undefined" class="flex justify-between">
          <span class="font-medium">Required:</span>
          <span>{{ workflowSteps[viewedStep].required ? 'Yes' : 'No' }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].order" class="flex justify-between">
          <span class="font-medium">Order:</span>
          <span>{{ workflowSteps[viewedStep].order }}</span>
        </div>
        
        <div v-if="workflowSteps[viewedStep].estimated_duration" class="flex justify-between">
          <span class="font-medium">Duration:</span>
          <span>{{ workflowSteps[viewedStep].estimated_duration }}</span>
        </div>

      </div>
    </div>

    <!-- Schema Fetch Error Notification -->
    <div v-if="!activeWorkflow?.entityDataStructure" :class="['rounded-lg p-4 border mt-4', colors.primary.bg, colors.secondary.border]">
      <h4 :style="{ fontSize: scaling.font.label }" :class="['font-medium mb-3 text-red-600 dark:text-red-400']">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Schema Fetch Failed
      </h4>
      
      <div :style="{ fontSize: scaling.font.label }" :class="[colors.secondary.text, 'space-y-2']">
        <div class="flex justify-between">
          <span class="font-medium">Status:</span>
          <span class="text-red-600 dark:text-red-400">Failed to load entity schema</span>
        </div>
        
        <div class="flex justify-between">
          <span class="font-medium">Error:</span>
          <span class="text-red-600 dark:text-red-400 text-right">404 Not Found</span>
        </div>
        
        <div class="flex justify-between">
          <span class="font-medium">API Endpoint:</span>
          <span class="font-mono text-gray-500">/api/entities/{{ activeWorkflow?.template?.entity || 'unknown' }}/schema</span>
        </div>
      </div>
    </div>

    <!-- Complete Table Structure - All Fields -->
    <div v-if="getCompleteTableStructure()" :class="['rounded-lg p-4 border mt-4', colors.primary.bg, colors.secondary.border]">
      <h4 :style="{ fontSize: scaling.font.label }" :class="['font-medium mb-3', colors.primary.textMuted]">
        <i class="fas fa-database mr-1 text-blue-600"></i>
        Complete Record Data
      </h4>
      
      <div :style="{ fontSize: scaling.font.label }" :class="[colors.secondary.text, 'space-y-2']">
        <div v-for="(value, key) in getCompleteTableStructure()" :key="key" class="flex justify-between">
          <span class="font-medium">{{ formatFieldName(key) }}:</span>
          <span v-if="typeof value === 'object' && value !== null" class="text-right">
            <div v-if="value.id && value.name" class="text-blue-600 dark:text-blue-400">
              {{ value.name }} (ID: {{ value.id }})
            </div>
            <div v-else class="font-mono" :style="{ fontSize: scaling.font.small }">
              {{ JSON.stringify(value, null, 2) }}
            </div>
          </span>
          <span v-else-if="value === null" class="text-gray-400 italic">Not filled</span>
          <span v-else class="text-right">{{ value }}</span>
        </div>
      </div>
    </div>

    <!-- PROGRESS BAR FOR CHECKLIST - UNDER KEY/VALUE PAIRS -->
    <div v-if="workflowSteps[viewedStep].type === 'checklist'" class="mt-6">
      <div class="w-full bg-gray-300 rounded-full h-3">
        <div class="bg-green-500 h-3 rounded-full" :style="{ width: `${getChecklistProgress(workflowSteps[viewedStep])}%` }"></div>
      </div>
       <!--<div :class="['mt-1 text-center', colors.secondary.text]" :style="{ fontSize: scaling.font.small }">{{ getCompletedChecklistItems(workflowSteps[viewedStep]) }} of {{ getTotalChecklistItems(workflowSteps[viewedStep]) }} items checked</div> -->
    </div>

    <!-- PROGRESS BAR FOR FORM SUBMISSION - UNDER KEY/VALUE PAIRS -->
    <div v-if="workflowSteps[viewedStep].type === 'form_submission'" class="mt-6">
      <div class="w-full bg-gray-300 rounded-full h-3">
        <div class="bg-blue-500 h-3 rounded-full" :style="{ width: `${getFormProgress(workflowSteps[viewedStep])}%` }"></div>
      </div>
      <!--<div :class="['mt-1 text-center', colors.secondary.text]"  :style="{ fontSize: scaling.font.small }">{{ getCompletedFormFields(workflowSteps[viewedStep]) }} of {{ getFormFieldCount(workflowSteps[viewedStep]) }} fields filled</div>-->
    </div>







  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
