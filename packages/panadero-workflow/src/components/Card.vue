<!--
  Workflow Card Component
  @version 1.1.0
  @description Redesigned workflow card with full dynamic font sizing using useScalingV2
-->
<script setup>
import { computed } from 'vue';

// Props
const props = defineProps({
  workflow: { 
    type: Object, 
    required: true 
  },
  scaling: { 
    type: Object, 
    required: true 
  },
  clickable: { 
    type: Boolean, 
    default: true 
  },
  showActions: { 
    type: Boolean, 
    default: false 
  }
})

// Emits
const emit = defineEmits(['click', 'select', 'start'])

// Methods
function handleClick() {
  if (props.clickable) {
    emit('click', props.workflow)
    emit('select', props.workflow)
  }
}

function getStepCount() {
  // Handle both workflow.steps array and workflow.steps number
  if (Array.isArray(props.workflow.steps)) {
    return props.workflow.steps.length
  }
  if (typeof props.workflow.steps === 'number') {
    return props.workflow.steps
  }
  return 0
}

function getDuration() {
  // Handle both estimated_duration and avgTime
  return props.workflow.estimated_duration || props.workflow.avgTime || 'N/A'
}

function getApprovalCount() {
  if (Array.isArray(props.workflow.steps)) {
    return props.workflow.steps.filter(s => s.type === 'approval').length
  }
  return 0
}

function getCategoryDisplay() {
  if (!props.workflow.category) return 'General'
  return props.workflow.category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

function getDifficultyColor(complexity) {
  switch (complexity) {
    case 'simple': return 'text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400'
    case 'moderate': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-400'
    case 'complex': return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-yellow-400'
    default: return 'text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-400'
  }
}

function getModuleDisplayName(module) {
  if (!module) return 'General'
  
  switch (module) {
    case 'gl': return 'General Ledger'
    case 'ap': return 'Accounts Payable'
    case 'ar': return 'Accounts Receivable'
    case 'cf': return 'Cash Flow'
    case 'fa': return 'Fixed Assets'
    case 'tax': return 'Tax Management'
    case 'budget': return 'Budgeting'
    case 'compliance': return 'Compliance'
    case 'report': return 'Reporting'
    case 'procurement': return 'Procurement'
    case 'hr': return 'Human Resources'
    case 'legal': return 'Legal'
    default: return module.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
  }
}
</script>

<template>
  <div 
    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-300 relative group hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600"
    :class="{ 
      'cursor-pointer': clickable,
      'p-4': true
    }"
    :style="{ 
      padding: scaling.space.medium,
      borderRadius: scaling.card.borderRadius
    }"
    @click="handleClick">
    
    <!-- Header with module badge -->
    <div class="flex justify-between items-start mb-3">
      <h3 
        :style="{ fontSize: scaling.font.title }" 
        class="font-semibold text-gray-900 dark:text-white cursor-pointer flex-1 line-clamp-2">
        {{ workflow.name }}
      </h3>
      <div class="flex items-center space-x-2">
        <span v-if="workflow.module"
              :style="{ fontSize: scaling.font.caption }"  
              class="px-3 py-1 font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 flex-shrink-0">
          {{ getModuleDisplayName(workflow.module) }}
        </span>
        <button 
          v-if="showActions"
          @click.stop="$emit('select', workflow)"
          :style="{ 
            width: scaling.icon.small,
            height: scaling.icon.small,
            fontSize: scaling.font.xs
          }"
          class="bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
          title="Select workflow">
          <i class="fas fa-check"></i>
        </button>
      </div>
    </div>
    
    <!-- Description -->
    <div 
      :style="{ fontSize: scaling.font.body }" 
      class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
      {{ workflow.description || 'No description available' }}
    </div>
    
    <!-- Stats Row - compact grid -->
    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="text-center p-3 bg-slate-100 dark:bg-slate-800 rounded-lg">
        <div 
          :style="{ fontSize: scaling.font.lg }" 
          class="font-bold text-gray-900 dark:text-white">
          {{ getStepCount() }}
        </div>
        <div 
          :style="{ fontSize: scaling.font.caption }" 
          class="text-gray-500 dark:text-gray-400">
          Steps
        </div>
      </div>
      <div class="text-center p-3 bg-slate-100 dark:bg-slate-800 rounded-lg">
        <div 
          :style="{ fontSize: scaling.font.lg }" 
          class="font-bold text-gray-900 dark:text-white">
          {{ getDuration() }}
        </div>
        <div 
          :style="{ fontSize: scaling.font.caption }" 
          class="text-gray-500 dark:text-gray-400">
          Duration
        </div>
      </div>
    </div>

    <!-- Details - compact spacing -->
    <div 
      :style="{ fontSize: scaling.font.caption }" 
      class="space-y-2 text-gray-600 dark:text-gray-400 mb-4">
      
      <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
        <span class="text-gray-600 dark:text-gray-400 flex items-center">
          <i class="fas fa-user-check mr-2" :style="{ fontSize: scaling.icon.small }"></i>
          Approvals
        </span>
        <span class="text-gray-900 dark:text-white font-medium">
          {{ getApprovalCount() }}
        </span>
      </div>
      
      <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
        <span class="text-gray-600 dark:text-gray-400 flex items-center">
          <i class="fas fa-layer-group mr-2" :style="{ fontSize: scaling.icon.small }"></i>
          Category
        </span>
        <span class="text-gray-900 dark:text-white font-medium capitalize">
          {{ getCategoryDisplay() }}
        </span>
      </div>
      
      <div v-if="workflow.complexity" class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
        <span class="text-gray-600 dark:text-gray-400 flex items-center">
          <i class="fas fa-signal mr-2" :style="{ fontSize: scaling.icon.small }"></i>
          Complexity
        </span>
        <span 
          :class="getDifficultyColor(workflow.complexity)"
          :style="{ fontSize: scaling.font.caption }"
          class="px-2 py-1 rounded-full font-medium">
          {{ workflow.complexity }}
        </span>
      </div>
    </div>
    
    <!-- Action section -->
    <div 
      class="pt-3 border-t border-gray-200 dark:border-gray-600"
      :style="{ marginTop: scaling.space.small }">
      
      <!-- Custom actions slot -->
      <slot name="actions">
        <!-- Default action hint -->
        <div v-if="clickable" 
             :style="{ fontSize: scaling.font.caption }" 
             class="text-gray-500 dark:text-gray-400 text-center">
          <i class="fas fa-mouse-pointer mr-2"></i>
          Click to start workflow
        </div>
        <!-- No action hint when not clickable -->
        <div v-else 
             :style="{ fontSize: scaling.font.caption }" 
             class="text-gray-500 dark:text-gray-400 text-center">
          <i class="fas fa-info-circle mr-2"></i>
          Workflow template
        </div>
      </slot>
    </div>
  </div>
</template>

<style scoped>
/* Enhanced hover effects */
.group:hover {
  transform: translateY(-2px);
}

/* Line clamp for text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>