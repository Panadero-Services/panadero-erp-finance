<!--
  Workflow Stepper Component
  @version 1.1.0
  @description Dynamic vertical stepper for workflow steps with ACTUAL step status checking
  @fixed Use real step.status instead of assuming steps before current are completed
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store
//const settings = useWorkflowSettings()

// Dynamic font sizes - ONLY changing styling references
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`
const _value = computed(() => props.scaling.font.base) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _padding = computed(() => `${props.scaling.space.small}`) // WAS: `${settings.fontSizesComputed.value.h4/2}px`

// Props
const props = defineProps({
  steps: {
    type: Array,
    default: () => []
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
const emit = defineEmits(['step-click'])

// Methods
function handleStepClick(stepIndex) {
  emit('step-click', stepIndex)
}

function getStepStatus(stepIndex) {
  const step = props.steps[stepIndex]
  if (!step) return 'pending'
  
  // Use ACTUAL step status from the workflow data
  if (step.status === 'completed' || step.status === 'approved') return 'completed'
  if (step.status === 'active' || stepIndex === props.currentStep) return 'active'
  if (step.status === 'requires_approval') return 'approval'
  if (step.status === 'failed') return 'failed'
  
  return 'pending'
}

function getStepClasses(stepIndex) {
  const status = getStepStatus(stepIndex)
  const baseClasses = 'flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 cursor-pointer'
  
  switch (status) {
    case 'completed':
      return `${baseClasses} bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 hover:bg-green-100 dark:hover:bg-green-900/30`
    case 'active':
      return `${baseClasses} bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 hover:bg-blue-100 dark:hover:bg-blue-900/30`
    case 'approval':
      return `${baseClasses} bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 hover:bg-yellow-100 dark:hover:bg-yellow-900/30`
    case 'failed':
      return `${baseClasses} bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 hover:bg-red-100 dark:hover:bg-red-900/30`
    default: // pending
      return `${baseClasses} bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700`
  }
}

function getStepIconClasses(stepIndex) {
  const status = getStepStatus(stepIndex)
  
  switch (status) {
    case 'completed':
      return 'w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center'
    case 'active':
      return 'w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center'
    case 'approval':
      return 'w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center'
    case 'failed':
      return 'w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center'
    default: // pending
      return 'w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center'
  }
}

function getStepIcon(stepIndex) {
  const status = getStepStatus(stepIndex)
  
  switch (status) {
    case 'completed': return 'fas fa-check'
    case 'active': return 'fas fa-play'
    case 'approval': return 'fas fa-clock'
    case 'failed': return 'fas fa-times'
    default: return stepIndex + 1 // Show step number for pending
  }
}
</script>

<template>
  <div class="space-y-2">
    <div 
      v-for="(step, index) in steps" 
      :key="step.id || index"
      :class="getStepClasses(index)"
      @click="handleStepClick(index)"
    >
      <!-- Step Icon -->
      <div :class="getStepIconClasses(index)">
        <i v-if="getStepStatus(index) !== 'pending'" :class="getStepIcon(index)" class="text-sm"></i>
        <span v-else :style="{ fontSize: _caption }" class="font-medium">{{ index + 1 }}</span>
      </div>
      
      <!-- Step Content -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
          <h4 :style="{ fontSize: _value }" class="font-medium text-gray-900 dark:text-white">
            {{ step.name || `Step ${index + 1}` }}
          </h4>
          <span 
            v-if="getStepStatus(index) !== 'pending'" 
            :style="{ fontSize: _caption }" 
            class="text-xs font-medium uppercase tracking-wide"
            :class="{
              'text-green-600 dark:text-green-400': getStepStatus(index) === 'completed',
              'text-blue-600 dark:text-blue-400': getStepStatus(index) === 'active',
              'text-yellow-600 dark:text-yellow-400': getStepStatus(index) === 'approval',
              'text-red-600 dark:text-red-400': getStepStatus(index) === 'failed'
            }"
          >
            {{ getStepStatus(index) }}
          </span>
        </div>
        
        <p v-if="step.description" :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400 mt-1">
          {{ step.description }}
        </p>
        
        <div v-if="step.type" :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-500 mt-1">
          {{ step.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>