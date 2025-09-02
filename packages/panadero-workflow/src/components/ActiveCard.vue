<!--
  Active Workflow Card Component
  @version 1.2.0
  @description Upgraded card for displaying active workflows with clean state information and NO debug clutter
  @changed Removed debug section, enhanced workflow state display, improved layout and progress visualization
-->
<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  activeWorkflow: { 
    type: Object, 
    required: true 
  },
  scaling: { 
    type: Object, 
    required: true 
  }
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
  <div 
    class="bg-green-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg hover:shadow-md transition-all duration-200 relative group"
    :style="{ 
      padding: scaling.space.medium,
      borderRadius: scaling.card.borderRadius
    }">
    
    <!-- Header -->
    <div class="flex justify-between items-start mb-3">
      <h3 
        :style="{ fontSize: scaling.font.title }"  
        class="font-semibold text-gray-900 dark:text-white cursor-pointer flex-1" 
        @click="$emit('open', activeWorkflow)">
        {{ activeWorkflow.name }}
      </h3>
      
      <div class="flex items-center space-x-2 ml-3">
        <!-- Status Badge -->
        <span 
          :style="{ fontSize: scaling.font.caption }" 
          class="px-3 py-1 font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full">
          {{ activeWorkflow.status.toUpperCase() }}
        </span>
        <!-- Delete Button -->
        <button 
          @click.stop="$emit('delete', activeWorkflow)"
          :style="{ 
            width: scaling.icon.small,
            height: scaling.icon.small
          }"
          class="hover:bg-red-400 text-black rounded-full flex items-center justify-center group-hover:opacity-100 transition-opacity"
          title="Delete workflow">
          <i 
            class="fas fa-times" 
            :style="{ fontSize: scaling.font.xs }">
          </i>
        </button>
      </div>
    </div>

    <!-- Workflow Details Grid -->
    <div 
      :style="{ fontSize: scaling.font.caption }" 
      class="grid grid-cols-2 gap-3 mb-3 text-gray-600 dark:text-gray-400">
      <div class="space-y-1">
        <div class="flex items-center space-x-2">
          <i 
            class="fas fa-hashtag" 
            :style="{ fontSize: scaling.icon.small }">
          </i>
          <span>{{ activeWorkflow.workflowNr }}</span>
        </div>
        <div class="flex items-center space-x-2">
          <i 
            class="fas fa-building" 
            :style="{ fontSize: scaling.icon.small }">
          </i>
          <span>{{ getModuleDisplayName(activeWorkflow.module) }}</span>
        </div>
      </div>
      
      <div class="space-y-1">
        <div class="flex items-center space-x-2">
          <i 
            class="fas fa-clock" 
            :style="{ fontSize: scaling.icon.small }">
          </i>
          <span>{{ formatStartTime(activeWorkflow.started_at) }}</span>
        </div>
        <div class="flex items-center space-x-2">
          <i 
            class="fas fa-list-ol" 
            :style="{ fontSize: scaling.icon.small }">
          </i>
          <span>{{ workflowSteps.length }} steps</span>
        </div>
      </div>
    </div>

    <!-- Progress Section -->
    <div class="mb-3">
      <div class="flex items-center justify-between mb-2">
        <span 
          :style="{ fontSize: scaling.font.sm }" 
          class="font-medium text-gray-700 dark:text-gray-300">
          Progress
        </span>
        <span 
          :style="{ fontSize: scaling.font.sm }" 
          class="text-blue-600 dark:text-blue-400 font-medium">
          {{ progressPercentage }}% • Step {{ currentStepIndex + 1 }}/{{ workflowSteps.length }}
        </span>
      </div>
      
      <!-- Progress Bar -->
      <div 
        class="w-full bg-gray-200 dark:bg-gray-700 rounded-full transition-all duration-300"
        :style="{ height: scaling.space.tiny }">
        <div 
          class="bg-blue-600 rounded-full transition-all duration-300"
          :style="{ 
            width: `${progressPercentage}%`,
            height: scaling.space.tiny
          }">
        </div>
      </div>
    </div>

    <!-- Step Progress Indicators -->
    <div 
      class="flex items-center space-x-1 cursor-pointer" 
      @click="$emit('open', activeWorkflow)">
      <div 
        v-for="(step, index) in workflowSteps" 
        :key="index" 
        class="rounded-sm border transition-colors duration-200 relative"
        :class="getSimpleStepClasses(step, index)"
        :style="{ 
          width: scaling.space.tiny,
          height: scaling.space.tiny
        }"
        :title="`Step ${index + 1}: ${step.name || 'Unnamed'}`">
        <!-- Current step indicator -->
        <div 
          v-if="index === currentStepIndex" 
          class="absolute inset-0 border border-blue-400 rounded-sm animate-pulse">
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Enhanced hover effects */
.group:hover {
  transform: translateY(-1px);
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Progress bar animation */
.progress-bar {
  transition: width 0.5s ease-out;
}
</style>