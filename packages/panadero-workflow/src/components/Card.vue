<!--
  Workflow Card Component
  @version 1.0.8
  @description Reusable workflow card with optional actions and flexible display modes
-->
<script setup>
import { computed } from 'vue';
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store
const settings = useWorkflowSettings()

// Props
const props = defineProps({
  workflow: {
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
    case 'complex': return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400'
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

// Add after the stores section:
// Dynamic font sizes (same as other components)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _body = computed(() => { return `${settings.fontSizesComputed.value.body}px`; });
const _h4 = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });

</script>

<template>
  <div 
    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-6 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-md transition-all"
    :class="{ 'cursor-pointer': clickable }"
    @click="handleClick">
    
    <!-- Header with module badge -->
    <div class="flex justify-between items-start mb-3">
      <div class="flex-1">
        <h3 :style="{ fontSize: _body }" class="font-semibold text-gray-900 dark:text-white mb-1 leading-tight">
          {{ workflow.name }}
        </h3>
        <p :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400 line-clamp-2">
          {{ workflow.description || 'No description available' }}
        </p>
      </div>
      <span v-if="workflow.module"
      :style="{ fontSize: _caption }"  
            class="ml-3 px-2 py-1 font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 flex-shrink-0">
        {{ getModuleDisplayName(workflow.module) }}
      </span>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="text-center p-2 bg-gray-100 dark:bg-gray-700/50 rounded">
        <div :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
          {{ getStepCount() }}
        </div>
        <div :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">Steps</div>
      </div>
      <div class="text-center p-2 bg-gray-100 dark:bg-gray-700/50 rounded">
        <div :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
          {{ getDuration() }}
        </div>
        <div :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">Duration</div>
      </div>
    </div>

    <!-- Details -->
    <div class="space-y-2 text-sm">
      <div class="flex items-center justify-between">
        <span :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-user-check mr-2"></i>Approvals
        </span>
        <span :style="{ fontSize: _caption }" class="text-gray-900 dark:text-white font-medium">
          {{ getApprovalCount() }}
        </span>
      </div>
      
      <div class="flex items-center justify-between">
        <span :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-layer-group mr-2"></i>Category
        </span>
        <span :style="{ fontSize: _caption }" class="text-gray-900 dark:text-white font-medium capitalize">
          {{ getCategoryDisplay() }}
        </span>
      </div>
      
      <div v-if="workflow.complexity" class="flex items-center justify-between">
        <span :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-signal mr-2"></i>Complexity
        </span>
        <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getDifficultyColor(workflow.complexity)">
          {{ workflow.complexity }}
        </span>
      </div>
    </div>
    
    <!-- Action hint or custom actions -->
    <div class="mt-4 pt-3  border-gray-200 dark:border-gray-600">
      <!-- Custom actions slot -->
      <slot name="actions">
        <!-- Default action hint -->
        <div v-if="clickable" :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400 text-center">
          <i class="fas fa-mouse-pointer mr-1"></i>
          Click to start workflow
        </div>
        <!-- No action hint when not clickable -->
        <div v-else :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400 text-center">
          <i class="fas fa-info-circle mr-1"></i>
          Workflow template
        </div>
      </slot>
    </div>
  </div>
</template>


