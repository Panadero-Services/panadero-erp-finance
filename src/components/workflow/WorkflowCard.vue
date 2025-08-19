<!--
  Workflow Card Component
  @version 1.0.8
  @description Reusable workflow card with stats and actions
-->
<template>
  <div 
    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-md transition-all cursor-pointer"
    @click="$emit('click', workflow)">
    
    <!-- Header with module badge -->
    <div class="flex justify-between items-start mb-3">
      <div class="flex-1">
        <h3 :style="{ fontSize: `${store.fontSizes.base}px` }" class="font-semibold text-gray-900 dark:text-white mb-1 leading-tight">
          {{ workflow.name }}
        </h3>
        <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 line-clamp-2">
          {{ workflow.description || 'No description available' }}
        </p>
      </div>
      <span v-if="workflow.module" 
            class="ml-3 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 flex-shrink-0">
        {{ getModuleDisplayName(workflow.module) }}
      </span>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
        <div :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">
          {{ workflow.steps?.length || 0 }}
        </div>
        <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">Steps</div>
      </div>
      <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
        <div :style="{ fontSize: `${store.fontSizes.base + 2}px` }" class="font-semibold text-gray-900 dark:text-white">
          {{ workflow.estimated_duration || 'N/A' }}
        </div>
        <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">Duration</div>
      </div>
    </div>

    <!-- Details -->
    <div class="space-y-2 text-sm">
      <div class="flex items-center justify-between">
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-user-check mr-2"></i>Approvals
        </span>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-900 dark:text-white font-medium">
          {{ workflow.steps?.filter(s => s.type === 'approval').length || 0 }}
        </span>
      </div>
      
      <div class="flex items-center justify-between">
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-layer-group mr-2"></i>Category
        </span>
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-900 dark:text-white font-medium capitalize">
          {{ workflow.category?.replace('_', ' ') || 'General' }}
        </span>
      </div>
      
      <div v-if="workflow.complexity" class="flex items-center justify-between">
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-400">
          <i class="fas fa-signal mr-2"></i>Complexity
        </span>
        <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getDifficultyColor(workflow.complexity)">
          {{ workflow.complexity }}
        </span>
      </div>
    </div>
    
    <!-- Action hint -->
    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
      <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 text-center">
        <i class="fas fa-mouse-pointer mr-1"></i>
        Click to start workflow
      </div>
    </div>
  </div>
</template>

<script setup>
import { useFinanceStore } from '../../stores/financeStore.js'

// Store
const store = useFinanceStore()

// Props
const props = defineProps({
  workflow: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['click'])

// Utility functions
function getDifficultyColor(complexity) {
  switch (complexity) {
    case 'simple': return 'text-green-600 bg-green-100'
    case 'moderate': return 'text-yellow-600 bg-yellow-100'
    case 'complex': return 'text-red-600 bg-red-100'
    default: return 'text-gray-600 bg-gray-100'
  }
}

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
</script>
