<!--
  Workflow Stepper Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Vertical stepper component for workflow progress display
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

// Get step type icon
function getStepTypeIcon(stepType) {
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

// Get step status based on actual step.status from workflow store
function getStepStatus(step, stepIdx, currentStep) {
  // Use the actual step status from the workflow store
  if (step.status) {
    return step.status
  }
  
  // Only fallback to position-based logic if no status is set
  // This should rarely happen as steps should have explicit status
  if (stepIdx === currentStep) {
    return 'active'  // Only the current step can be active
  } else {
    return 'pending'  // All other steps are pending until explicitly completed
  }
}
</script>

<template>
  <!-- Vertical Stepper with Progress Bars -->
  <div class="flex-1 overflow-y-auto p-2 mt-0">
    <!-- Stepper Header -->
    <div class="mb-4">
      <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
        Workflow Steps {{ currentStep + 1 }}/{{ workflowSteps.length }}
      </h3>
    </div>
    
    <nav aria-label="Workflow Progress" class="bg-gray-100 dark:bg-gray-800 p-4 border dark:border-gray-700 rounded-lg relative">
      <ol role="list" class="space-y-3">
        <li v-for="(step, stepIdx) in workflowSteps" :key="stepIdx" 
            :class="[stepIdx !== workflowSteps.length - 1 ? 'pb-10' : '', 'relative']">
          
          <!-- Progress Line -->
          <div v-if="stepIdx !== workflowSteps.length - 1" 
               :style="{ 
                 position: 'absolute', 
                 width: '1px', 
                 height: '48px', 
                 backgroundColor: getStepStatus(step, stepIdx, currentStep) === 'completed' ? '#4f46e5' : '#9ca3af', 
                 left: '8px', 
                 top: '12px', 
                 zIndex: 10 
               }"
               aria-hidden="true" />
          
          <!-- Step Content -->
          <div class="group relative flex items-start">
            <!-- Step Circle -->
            <span class="flex items-center" :style="{ height: scaling.icon.medium }">
              <span class="relative z-10 flex items-center justify-center rounded-full"
                    :style="{ width: scaling.icon.medium, height: scaling.icon.medium }"
                    :class="getStepStatus(step, stepIdx, currentStep) === 'completed' ? 'bg-indigo-600 group-hover:bg-indigo-800 dark:bg-indigo-500 dark:group-hover:bg-indigo-600' : 
                           getStepStatus(step, stepIdx, currentStep) === 'active' ? 'border border-indigo-600 bg-white dark:border-indigo-500 dark:bg-gray-900' : 
                           'border border-gray-300 bg-white group-hover:border-gray-400 dark:border-white/15 dark:bg-gray-900 dark:group-hover:border-white/25'">
                
                <!-- Check Icon for completed steps -->
                <i v-if="getStepStatus(step, stepIdx, currentStep) === 'completed'" class="fas fa-check text-white" :style="{ fontSize: scaling.font.caption }"></i>
                
                <!-- Current step indicator -->
                <span v-else-if="getStepStatus(step, stepIdx, currentStep) === 'active'" 
                      :style="{ width: scaling.space.small, height: scaling.space.small }"
                      class="rounded-full bg-indigo-600 dark:bg-indigo-500"></span>
                
                <!-- Upcoming step indicator -->
                <span v-else 
                      :style="{ width: scaling.space.small, height: scaling.space.small }"
                      class="rounded-full bg-transparent group-hover:bg-gray-300 dark:group-hover:bg-white/15"></span>
              </span>
            </span>
            
            <!-- Step Info -->
            <span class="ml-3 flex min-w-0 flex-col flex-1">
              <div class="flex items-center justify-between">
                <span :style="{ fontSize: scaling.font.body }" class="font-medium "
                      :class="getStepStatus(step, stepIdx, currentStep) === 'completed' ? 'text-gray-900 dark:text-white' : 
                             getStepStatus(step, stepIdx, currentStep) === 'active' ? 'text-indigo-600 dark:text-indigo-400' : 
                             'text-gray-700 dark:text-gray-300'">
                  {{ step.name || `Step ${stepIdx}` }}
                </span>
                <i v-if="step.type" :class="getStepTypeIcon(step.type)" :style="{ fontSize: scaling.font.small }"></i>
              </div>
              <span :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
                {{ step.description || 'Step description' }}
              </span>
            </span>
          </div>
        </li>
      </ol>
    </nav>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>