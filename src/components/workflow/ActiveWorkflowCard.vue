<!--
  Active Workflow Card Component
  @version 1.0.8
  @description Card for displaying active workflows with delete functionality
-->
<script setup>
import { useFinanceStore } from '../../stores/financeStore.js'

// Store
const store = useFinanceStore()

// Props
const props = defineProps({
  activeWorkflow: {
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
</script>

<template>
  <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
    <div class="flex justify-between items-start mb-2">
      <h3 :style="{ fontSize: `${store.fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white cursor-pointer" @click="$emit('open', activeWorkflow)">
        {{ activeWorkflow.name }}
      </h3>
      <div class="flex items-center space-x-2">
        <span :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="px-2 py-1 font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full">
          {{ activeWorkflow.status }}
        </span>
        <button 
          @click.stop="$emit('delete', activeWorkflow)"
          class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
          title="Delete workflow">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="space-y-1 text-gray-600 dark:text-gray-400 cursor-pointer" @click="$emit('open', activeWorkflow)">
      <div>
        <i class="fas fa-hashtag mr-2"></i>
        {{ activeWorkflow.workflowNr }}
      </div>
      <div>
        <i class="fas fa-tag mr-2"></i>
        {{ getModuleDisplayName(activeWorkflow.module) }}
      </div>
      <div>
        <i class="fas fa-clock mr-2"></i>
        Started {{ activeWorkflow.startedAt.toLocaleTimeString() }}
      </div>
    </div>
  </div>
</template>
