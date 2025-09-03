<!--
  Workflow Step Info Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Step information component - displays template information with dynamic styling
-->
<script setup>
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
  scaling: {
    type: Object,
    required: true
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

// Get step status text
function getStepStatusText(step) {
  if (!step) return 'Unknown'
  
  // Check if step is completed
  if (step.status === 'completed') return 'Completed'
  if (step.status === 'active') return 'In Progress'
  if (step.status === 'pending') return 'Pending'
  
  // Check completion criteria based on step type
  switch (step.type) {
    case 'shared_entity_selection':
      return step.data?.selectedEntity ? 'Ready' : 'Entity Required'
      
    case 'form_submission':
      if (!step.form_schema?.sections) return 'No Form Schema'
      const totalFields = getFormFieldCount(step)
      const completedFields = getCompletedFormFields(step)
      return completedFields === totalFields ? 'Ready' : `${completedFields}/${totalFields} Fields`
      
    case 'approval':
      if (!step.approvers?.length) return 'No Approvers Defined'
      const approvedCount = getApprovedCount(step)
      return approvedCount === step.approvers.length ? 'Approved' : `${approvedCount}/${step.approvers.length} Approved`
      
    case 'checklist':
      if (!step.checklist_items?.length) return 'No Items Defined'
      const completedItems = getCompletedChecklistItems(step)
      return completedItems === step.checklist_items.length ? 'Ready' : `${completedItems}/${step.checklist_items.length} Items`
      
    default:
      return 'Unknown'
  }
}

// Get step status CSS class
function getStepStatusClass(step) {
  if (!step) return 'text-gray-500'
  
  if (step.status === 'completed') return 'text-green-600'
  if (step.status === 'active') return 'text-blue-600'
  if (step.status === 'pending') return 'text-gray-500'
  
  // Check completion criteria
  switch (step.type) {
    case 'shared_entity_selection':
      return step.data?.selectedEntity ? 'text-green-600' : 'text-red-600'
      
    case 'form_submission':
      if (!step.form_schema?.sections) return 'text-gray-500'
      const totalFields = getFormFieldCount(step)
      const completedFields = getCompletedFormFields(step)
      return completedFields === totalFields ? 'text-green-600' : 'text-orange-600'
      
    case 'approval':
      if (!step.approvers?.length) return 'text-gray-500'
      const approvedCount = getApprovedCount(step)
      return approvedCount === step.approvers.length ? 'text-green-600' : 'text-orange-600'
      
    case 'checklist':
      if (!step.checklist_items?.length) return 'text-gray-500'
      const completedItems = getCompletedChecklistItems(step)
      return completedItems === step.checklist_items.length ? 'text-green-600' : 'text-orange-600'
      
    default:
      return 'text-gray-500'
  }
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
</script>

<template>

  <!-- Vertical Info Column 2 -->
  <div class="flex-1 overflow-y-auto p-2 ">
    <!-- Stepper Header -->
    <div class="mb-4">
      <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
        Step Information
      </h3>
    </div>
    

    <!-- Header with Step Type + Colored Icon -->
    <div class="mb-4 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
      <div v-if="workflowSteps[currentStep]" class="flex items-center">
        <i :class="getStepIcon(workflowSteps[currentStep].type)" class="mr-2"></i>
        <span :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 capitalize ">
          {{ workflowSteps[currentStep].type?.replace(/_/g, ' ') || 'Unknown' }}
        </span>
      </div>
    </div>

    <!-- Template Information Body -->
    <div v-if="workflowSteps[currentStep]" class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
      <div :style="{ fontSize: scaling.font.label }" class="text-gray-600 dark:text-gray-400 space-y-2">
        <!-- Step Status -->
        <div class="flex justify-between">
          <span class="font-medium">Status:</span>
          <span :class="getStepStatusClass(workflowSteps[currentStep])" class="font-medium">
            {{ getStepStatusText(workflowSteps[currentStep]) }}
          </span>
        </div>
        
        <div v-if="workflowSteps[currentStep].entity_type" class="flex justify-between">
          <span class="font-medium">Entity Type:</span>
          <span>{{ workflowSteps[currentStep].entity_type }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].entity_selection?.api_endpoint" class="flex justify-between">
          <span class="font-medium">API Endpoint:</span>
          <span class="font-mono">{{ workflowSteps[currentStep].entity_selection.api_endpoint }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].entity_selection?.allow_create_new !== undefined" class="flex justify-between">
          <span class="font-medium">Allow Create New:</span>
          <span>{{ workflowSteps[currentStep].entity_selection.allow_create_new ? 'Yes' : 'No' }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].form_schema?.sections" class="flex justify-between">
          <span class="font-medium">Total Fields:</span>
          <span>{{ getFormFieldCount(workflowSteps[currentStep]) }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].form_schema?.sections" class="flex justify-between">
          <span class="font-medium">Sections:</span>
          <span>{{ workflowSteps[currentStep].form_schema.sections.length }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].approvers?.length" class="flex justify-between">
          <span class="font-medium">Approvers:</span>
          <span>{{ workflowSteps[currentStep].approvers.length }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].approval_required !== undefined" class="flex justify-between">
          <span class="font-medium">Approval Required:</span>
          <span>{{ workflowSteps[currentStep].approval_required ? 'Yes' : 'No' }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].checklist_items?.length" class="flex justify-between">
          <span class="font-medium">Checklist Items:</span>
          <span>{{ workflowSteps[currentStep].checklist_items.length }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].required !== undefined" class="flex justify-between">
          <span class="font-medium">Required:</span>
          <span>{{ workflowSteps[currentStep].required ? 'Yes' : 'No' }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].order" class="flex justify-between">
          <span class="font-medium">Order:</span>
          <span>{{ workflowSteps[currentStep].order }}</span>
        </div>
        
        <div v-if="workflowSteps[currentStep].estimated_duration" class="flex justify-between">
          <span class="font-medium">Duration:</span>
          <span>{{ workflowSteps[currentStep].estimated_duration }}</span>
        </div>





      </div>
    </div>


        <!-- PROGRESS BAR FOR FORM SUBMISSION - UNDER KEY/VALUE PAIRS -->
        <div v-if="workflowSteps[currentStep].type === 'form_submission'" class="mt-6">
          <div class="w-full bg-gray-300 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full" :style="{ width: `${getFormProgress(workflowSteps[currentStep])}%` }"></div>
          </div>
          <div class="text-xs text-gray-600 mt-1 text-center">{{ getCompletedFormFields(workflowSteps[currentStep]) }} of {{ getFormFieldCount(workflowSteps[currentStep]) }} fields filled</div>
        </div>


    <!-- PROGRESS BAR FOR CHECKLIST - UNDER KEY/VALUE PAIRS -->
    <div v-if="workflowSteps[currentStep].type === 'checklist'" class="mt-6">
      <div class="w-full bg-gray-300 rounded-full h-3">
        <div class="bg-green-500 h-3 rounded-full" :style="{ width: `${getChecklistProgress(workflowSteps[currentStep])}%` }"></div>
      </div>
      <div class="text-xs text-gray-600 mt-1 text-center">{{ getCompletedChecklistItems(workflowSteps[currentStep]) }} of {{ getTotalChecklistItems(workflowSteps[currentStep]) }} items checked</div>
    </div>

  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
