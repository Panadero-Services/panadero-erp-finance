<!--
  Active Workflow Card Component
  @version 1.2.0
  @description Upgraded card for displaying active workflows with clean state information and NO debug clutter
  @changed Removed debug section, enhanced workflow state display, improved layout and progress visualization
-->
<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  activeWorkflow: { type: Array, required: true },
  fontSizes: { type: Object, default: true}
})

// Emits
const emit = defineEmits(['open', 'delete'])

// Utility function
function getModuleDisplayName(module) {
  if (!module) return 'General'
  
  switch (module) {
    case 'gl': return 'General Ledger'
    case 'ap': return 'Accounts Payable'
    case 'ar': return 'Accounts Receivable'
    case 'cf': return 'Cash Flow'
    case 'procurement': return 'Procurement'
    default: return module.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
  }
}

// Computed properties for workflow state - FIXED: Ensure steps is always an array
const workflowSteps = computed(() => {
  const steps = props.activeWorkflow.steps
  return Array.isArray(steps) ? steps : []
})
const completedSteps = computed(() => workflowSteps.value.filter(step => step.status === 'completed'))
const currentStepIndex = computed(() => props.activeWorkflow.currentStep || 0)

// Template information - CLEAR distinction between template and workflow data
const templateInfo = computed(() => props.activeWorkflow.template || {})
const workflowData = computed(() => props.activeWorkflow.data || {})

// Template metadata
const templateName = computed(() => templateInfo.value.name || 'Unknown Template')
const templateDescription = computed(() => templateInfo.value.description || 'No description available')
const estimatedDuration = computed(() => templateInfo.value.estimatedDuration || 'N/A')
const templateSteps = computed(() => templateInfo.value.steps || 0)

// Dynamic font sizes - FULL dynamic sizing
const _base = computed(() => `${props.fontSizes.base}`)
const _xs = computed(() => `${props.fontSizes.xs}`)
const _xxs = computed(() => `${props.fontSizes.xxs}`)
const _xxxs = computed(() => `${props.fontSizes.xxxs}`)
const _body = computed(() => `${props.fontSizes.body}`)
const _caption = computed(() => `${props.fontSizes.caption}`)
const _h1 = computed(() => `${props.fontSizes.h1}`)
const _h2 = computed(() => `${props.fontSizes.h2}`)
const _h3 = computed(() => `${props.fontSizes.h3}`)
const _h4 = computed(() => `${props.fontSizes.h4}`)

// Progress calculation
const progressPercentage = computed(() => {
  if (workflowSteps.value.length === 0) return 0
  return Math.round(((currentStepIndex.value + 1) / workflowSteps.value.length) * 100)
})

// Format time helper
const formatStartTime = (timestamp) => {
  if (!timestamp) return 'Unknown'
  try {
    const date = new Date(timestamp)
    return date.toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit',
      hour12: false 
    })
  } catch {
    return 'Unknown'
  }
}

// Simple step visualization
function getSimpleStepClasses(step, index) {
  if (step.status === 'completed') {
    return 'bg-green-500 border-green-600'
  } else if (step.status === 'active') {
    return 'bg-blue-500 border-blue-600'
  } else if (step.status === 'requires_approval') {
    return 'bg-yellow-500 border-yellow-600'
  } else if (step.status === 'failed') {
    return 'bg-red-500 border-red-600'
  } else {
    return 'bg-gray-200 dark:bg-gray-700'
  }
}
</script>

<template>
  <div class="bg-green-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg  hover:shadow-md transition-all duration-200 relative group">
    
    <div class="border-t border-green-600 rounded-lg" :class="_base<16 ? _base<12 ? 'p-2' : 'p-3' : 'p-4'">
    <!-- Header -->
    <div class="flex justify-between items-start mb-3">
      <h3 :style="{ fontSize: _caption }"  class="font-semibold text-gray-900 dark:text-white cursor-pointer flex-1" @click="$emit('open', activeWorkflow)">
        {{ activeWorkflow.name }}
      </h3>
      
      <div class="flex items-center space-x-2 ml-3">
        <!-- Status Badge -->
        <span :style="{ fontSize: _xxs }" class="px-2 py-1 font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full">
          {{ activeWorkflow.status.toUpperCase() }}
        </span>
        <!-- Delete Button -->
        <button 
          @click.stop="$emit('delete', activeWorkflow)"
          :class="_base<14 ? _base<12 ? 'w-4 h-4' : 'w-5 h-5' : 'w-6 h-6'"
          class="hover:bg-red-400 text-black rounded-full flex items-center justify-center  group-hover:opacity-100 transition-opacity"
          title="Delete workflow">
          <i class="fas fa-times" :style="{ fontSize: _xxs }" ></i>
        </button>
      </div>
    </div>

    <!-- Workflow Details Grid -->
    <div :style="{ fontSize: _xxs }" class="grid grid-cols-2 gap-3 mb-3 text-gray-600 dark:text-gray-400">
      <div class="space-y-1">
        <div class="flex items-center space-x-2">
          <i class="fas fa-hashtag text-xs"></i>
          <span>{{ activeWorkflow.workflowNr }}</span>
        </div>
        <div class="flex items-center space-x-2">
          <i class="fas fa-building text-xs"></i>
          <span>{{ getModuleDisplayName(activeWorkflow.module) }}</span>
        </div>
      </div>
      
      <div class="space-y-1">
        <div class="flex items-center space-x-2">
          <i class="fas fa-clock text-xs"></i>
          <span>{{ formatStartTime(activeWorkflow.started_at) }}</span>
        </div>
        <div class="flex items-center space-x-2">
          <i class="fas fa-list-ol text-xs"></i>
          <span>{{ workflowSteps.length }} steps</span>
        </div>
      </div>
    </div>

    <!-- Progress Section -->
    <div class="mb-3">
      <div class="flex items-center justify-between mb-2">
        <span :style="{ fontSize: _xs }" class="font-medium text-gray-700 dark:text-gray-300">
          Progress
        </span>
        <span :style="{ fontSize: _xs }" class="text-blue-600 dark:text-blue-400 font-medium">
          {{ progressPercentage }}% • Step {{ currentStepIndex + 1 }}/{{ workflowSteps.length }}
        </span>
      </div>
      
      <!-- Progress Bar -->
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div 
          :class="_base<16 ? _base<12 ? 'h-1' : 'h-1.5' : 'h-2'"
          class="bg-blue-600 rounded-full transition-all duration-300"
          :style="{ width: `${progressPercentage}%` }">
        </div>
      </div>
    </div>

    <!-- Step Progress Indicators -->
    <div class="flex items-center space-x-1 cursor-pointer" @click="$emit('open', activeWorkflow)">
      <div v-for="(step, index) in workflowSteps" :key="index" 
           class="w-3 h-3 rounded-sm border transition-colors duration-200 relative"
           :class="getSimpleStepClasses(step, index)"
           :title="`Step ${index + 1}: ${step.name || 'Unnamed'}`">
        <!-- Current step indicator -->
        <div v-if="index === currentStepIndex" 
             class="absolute inset-0 border border-blue-400 rounded-sm animate-pulse"></div>
      </div>
    </div>
  </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>