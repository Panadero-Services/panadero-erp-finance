<!--
  Workflow Card Component
  @version 1.1.0
  @description Redesigned workflow card matching ActiveCard.vue layout with full dynamic font sizing
-->
<script setup>
import { computed } from 'vue';
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store
const settings = useWorkflowSettings()

// Props
const props = defineProps({
  workflow: { type: Object, required: true },
  fontSizes: { type: Object, default: true },
  clickable: { type: Boolean, default: true },
  showActions: { type: Boolean, default: false }
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

// Dynamic font sizes - FULL dynamic sizing like ActiveCard.vue
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

const _padding = computed(() => `${_base}/2}px`)


</script>

<template>
  <div 
    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-colors relative group"
    :class="{ 'cursor-pointer': clickable }"
    @click="handleClick">
    
    <!-- Header with module badge - matching ActiveCard.vue layout -->
    <div class="flex justify-between items-start mb-2">
      <h3 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white cursor-pointer flex-1">
        {{ workflow.name }}
      </h3>
      <div class="flex items-center space-x-2">
        <span v-if="workflow.module"
              :style="{ fontSize: _caption }"  
              class="px-2 py-1 font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 flex-shrink-0">
          {{ getModuleDisplayName(workflow.module) }}
        </span>
        <button 
          v-if="showActions"
          @click.stop="$emit('select', workflow)"
          class="w-6 h-6 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
          title="Select workflow">
          <i class="fas fa-check"></i>
        </button>
      </div>
    </div>
    
    <!-- Description - matching ActiveCard.vue spacing -->
    <div :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
      {{ workflow.description || 'No description available' }}
    </div>
    
    <!-- Stats Row - compact like ActiveCard.vue -->
    <div class="grid grid-cols-2 gap-2 mb-3">
      <div class="text-center p-2 bg-slate-100 dark:bg-blue-900 rounded">
        <div :style="{ fontSize: _body }" class="font-semibold text-gray-900 dark:text-white">
          {{ getStepCount() }}
        </div>
        <div :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">Steps</div>
      </div>
      <div class="text-center p-2 bg-slate-100 dark:bg-blue-900 rounded">
        <div :style="{ fontSize: _body }" class="font-semibold text-gray-900 dark:text-white">
          {{ getDuration() }}
        </div>
        <div :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">Duration</div>
      </div>
    </div>

    <!-- Details - compact spacing like ActiveCard.vue -->
    <div :style="{ fontSize: _caption }" class="space-y-1 text-gray-600 dark:text-gray-400 mb-3">
      <div class="flex items-center justify-between">
        <span class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-user-check mr-2"></i>Approvals
        </span>
        <span class="text-gray-900 dark:text-white font-medium">
          {{ getApprovalCount() }}
        </span>
      </div>
      
      <div class="flex items-center justify-between">
        <span class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-layer-group mr-2"></i>Category
        </span>
        <span class="text-gray-900 dark:text-white font-medium capitalize">
          {{ getCategoryDisplay() }}
        </span>
      </div>
      
      <div v-if="workflow.complexity" class="flex items-center justify-between">
        <span class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-signal mr-2"></i>Complexity
        </span>
        <span class="text-gray-900 dark:text-gray-400 font-medium capitalize">
          {{ workflow.complexity }}
        </span>
      </div>
    </div>
    
    <!-- Action hint or custom actions - matching ActiveCard.vue border style -->
    <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
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

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>