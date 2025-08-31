<!--
  Workflow Start Overlay Component
  @version 1.1.0
  @description Modal overlay for starting workflow with details and confirmation - modular extraction
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import { useWorkflowDashboard } from '../composables/useWorkflowDashboard.js'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  selectedWorkflow: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'start-workflow'])

// Composables
const settings = useWorkflowSettings()
const { getModuleDisplayName } = useWorkflowDashboard()

// Dynamic font sizes
const _value = computed(() => `${settings.fontSizesComputed.value.h4}px`)
const _caption = computed(() => `${settings.fontSizesComputed.value.caption}px`)

// Methods
function handleClose() {
  emit('close')
}

function handleStartWorkflow() {
  emit('start-workflow', props.selectedWorkflow)
}

function handleOverlayClick(event) {
  if (event.target === event.currentTarget) {
    handleClose()
  }
}
</script>

<template>
  <div v-if="show" 
       class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
       @click="handleOverlayClick">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-4xl w-full mx-8 shadow-2xl" @click.stop>
      <!-- Header -->
      <div class="flex justify-between items-start mb-4">
        <div>
          <h3 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
            Start Workflow
          </h3>
          <p :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">
            {{ selectedWorkflow?.name }}
          </p>
        </div>
        <button @click="handleClose" 
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      
      <!-- Workflow Details -->
      <div class="mb-6 space-y-3">
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
          <p :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-300">
            {{ selectedWorkflow?.description || 'No description available' }}
          </p>
        </div>
        
        <div :style="{ fontSize: _caption }" class="grid grid-cols-2 gap-4">
          <div>
            <span class="text-gray-500 dark:text-gray-400">Steps:</span>
            <span class="ml-2 font-medium text-gray-900 dark:text-white">
              {{ selectedWorkflow?.steps?.length || selectedWorkflow?.steps || 0 }}
            </span>
          </div>
          <div>
            <span class="text-gray-500 dark:text-gray-400">Duration:</span>
            <span class="ml-2 font-medium text-gray-900 dark:text-white">
              {{ selectedWorkflow?.estimated_duration || selectedWorkflow?.avgTime || 'N/A' }}
            </span>
          </div>
          <div>
            <span class="text-gray-500 dark:text-gray-400">Module:</span>
            <span class="ml-2 font-medium text-gray-900 dark:text-white">
              {{ getModuleDisplayName(selectedWorkflow?.module) }}
            </span>
          </div>
          <div>
            <span class="text-gray-500 dark:text-gray-400">Complexity:</span>
            <span class="ml-2 font-medium text-gray-900 dark:text-white capitalize">
              {{ selectedWorkflow?.complexity || 'Medium' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex space-x-3">
        <button 
          @click="handleClose"
          class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          Cancel
        </button>
        <button 
          @click="handleStartWorkflow"
          class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <i class="fas fa-play mr-2"></i>
          Start Workflow
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
