<!--
  Workflow Stepper Component
  @version 1.0.10
  @description Dynamic vertical stepper for workflow steps with 5 step types
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowStore } from '../composables/workflowStore.js'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store
const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

// Dynamic font sizes (same as Statistics.vue, Dashboard.vue, and Modal.vue)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });

// Props
const props = defineProps({
  steps: {
    type: Array,
    default: () => []
  },
  currentStep: {
    type: Number,
    default: 0
  }
})

// Emits
const emit = defineEmits(['step-click'])

// Methods
function handleStepClick(stepIndex) {
  emit('step-click', stepIndex)
}

function getStepStatus(stepIndex) {
  if (stepIndex < props.currentStep) return 'completed'
  if (stepIndex === props.currentStep) return 'active'
  return 'pending'
}

function getStepClasses(stepIndex) {
  const status = getStepStatus(stepIndex)
  const baseClasses = 'flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 cursor-pointer'
  
  switch (status) {
    case 'completed':
      return `${baseClasses} bg-green-50 dark:bg-green-900/20 border border-gray-300 dark:border-gray-600 hover:bg-green-100 dark:hover:bg-green-900/30`
    case 'active':
      return `${baseClasses} bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-600 dark:border-blue-400`
    default:
      return `${baseClasses} bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700`
  }
}

function getStepNumberClasses(stepIndex) {
  const status = getStepStatus(stepIndex)
  const baseClasses = 'flex items-center justify-center w-8 h-8 rounded-full font-semibold transition-all duration-200'
  
  switch (status) {
    case 'completed':
      return `${baseClasses} bg-green-600 text-white`
    case 'active':
      return `${baseClasses} bg-blue-600 text-white`
    default:
      return `${baseClasses} bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300`
  }
}

function getStepTextClasses(stepIndex) {
  const status = getStepStatus(stepIndex)
  const baseClasses = 'font-medium transition-all duration-200'
  
  switch (status) {
    case 'completed':
      return `${baseClasses} text-green-800 dark:text-green-200`
    case 'active':
      return `${baseClasses} text-blue-800 dark:text-blue-200`
    default:
      return `${baseClasses} text-gray-700 dark:text-gray-300`
  }
}

function getStepDescriptionClasses(stepIndex) {
  const status = getStepStatus(stepIndex)
  const baseClasses = 'transition-all duration-200'
  
  switch (status) {
    case 'completed':
      return `${baseClasses} text-green-600 dark:text-green-400`
    case 'active':
      return `${baseClasses} text-blue-600 dark:text-blue-400`
    default:
      return `${baseClasses} text-gray-500 dark:text-gray-400`
  }
}

function getStepTypeInfo(stepType) {
  switch (stepType) {
    case 'user_actions':
      return { icon: 'fas fa-mouse-pointer', color: 'text-purple-600' }
    case 'form_submission':
      return { icon: 'fas fa-edit', color: 'text-blue-600' }
    case 'approval':
      return { icon: 'fas fa-user-check', color: 'text-orange-600' }
    case 'shared_entity_selection':
      return { icon: 'fas fa-database', color: 'text-green-600' }
    case 'checklist':
      return { icon: 'fas fa-tasks', color: 'text-indigo-600' }
    default:
      return { icon: 'fas fa-circle', color: 'text-gray-600' }
  }
}

// Format step type for display
function formatStepType(type) {
  return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}
</script>

<template>
  <div class="space-y-4">
    <div v-for="(step, index) in props.steps" 
         :key="step.id" 
         @click="handleStepClick(index)"
         :class="[
           'p-3 rounded-lg cursor-pointer transition-colors relative group',
           getStepClasses(index)
         ]">
      
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-3">
          <div :class="[
            'w-6 h-6 rounded-full flex items-center justify-center font-medium',
            getStepNumberClasses(index)
          ]"
                        :style="{ fontSize: _caption, padding: _padding }">
            {{ step.order || index + 1 }}
          </div>
          <span :style="{ fontSize: _caption }" 
                :class="['font-medium', getStepTextClasses(index)]">
            {{ step.name }}
          </span>
        </div>
        
        <!-- Active Step Badge -->
        <span v-if="getStepStatus(index) === 'active'" 
              :style="{ fontSize: _caption }" 
              class="px-2 py-1 font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full">
        </span>
      </div>
      
      <!-- Step Description -->
      <p :style="{ fontSize: _caption }" 
         :class="getStepDescriptionClasses(index)">
        {{ step.description }}
      </p>
      
      <!-- Step Type with Icon -->
      <div class="flex items-center mt-2 space-x-2">
        <i :class="[getStepTypeInfo(step.type).icon, getStepTypeInfo(step.type).color]" 
           :style="{ fontSize: _caption }"></i>
        <span :style="{ fontSize: _caption }" 
              :class="getStepDescriptionClasses(index)">
          {{ formatStepType(step.type) }}
        </span>
        <span v-if="step.estimatedDuration" 
              :style="{ fontSize: _caption }" 
              :class="getStepDescriptionClasses(index)">
          • {{ step.estimatedDuration }}
        </span>
        <span v-if="step.approvalRequired" 
              :style="{ fontSize: _caption }" 
              :class="['text-orange-600', getStepDescriptionClasses(index)]">
          • Requires Approval
        </span>
      </div>
      
      <!-- Completion Status for completed steps -->
      <p v-if="getStepStatus(index) === 'completed'" 
         :style="{ fontSize: _caption }" 
         class="text-green-600 dark:text-green-400 mt-2">
        <i class="fas fa-check mr-1 "></i>
        Completed
      </p>
      
      <!-- Hover Tooltip -->
      <div class="absolute left-full ml-2 top-0 bg-black text-white rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap"
           :style="{ fontSize: _caption }">
        {{ step.description }}
        <span v-if="step.estimatedDuration"> (~{{ step.estimatedDuration }})</span>
        <span v-if="step.approverRoles && step.approverRoles.length > 0">
          , {{ step.approverRoles.join(', ') }}
        </span>
      </div>
    </div>
    
    <!-- No Steps Available -->
    <div v-if="props.steps.length === 0" class="text-center py-8">
      <i class="fas fa-list-ol text-gray-400 text-2xl mb-2"></i>
      <p :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">
        No workflow steps available
      </p>
    </div>
  </div>
</template>