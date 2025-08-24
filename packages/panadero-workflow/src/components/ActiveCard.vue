<!--
  Active Workflow Card Component
  @version 1.0.8
  @description Card for displaying active workflows with delete functionality
-->
<script setup>
import { ref, computed } from 'vue'

import { useWorkflowStore } from '../composables/workflowStore.js'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Store

const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

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

// Dynamic font sizes (same as other components)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _base = computed(() => { return `${settings.fontSizesComputed.value.base}px`; });
</script>

<template>
  <div class="bg-green-50 dark:bg-green-900/20 border border-gray-300 dark:border-gray-500 rounded-lg p-4 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors relative group">
    <div class="flex justify-between items-start mb-2">
      <h3 :style="{ fontSize: _caption }" class="font-medium text-gray-900 dark:text-white cursor-pointer" @click="$emit('open', activeWorkflow)">
        {{ activeWorkflow.name }}
      </h3>
      <div class="flex items-center space-x-2">
        <span :style="{ fontSize: _caption }" class="px-2 py-1 font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full">
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
    <div :style="{ fontSize: _caption }" class="space-y-1 text-gray-600 dark:text-gray-400 cursor-pointer" @click="$emit('open', activeWorkflow)">
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
