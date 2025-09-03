\<!--
  Modal Footer Component
  @version 1.1.4
  @description Modal footer with simplified store usage
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
//import { useWorkflowStore } from '../composables/workflowStore.js'

// Props
const props = defineProps({
  workflowSteps: {
    type: Array,
    default: () => []
  },
  activeWorkflow: {
    type: Object,
    default: null
  },
  currentStep: {
    type: Number,
    default: 0
  },
  viewedStep: {
    type: Number,
    default: 0
  },
  workflowStore: {
    type: Object,
    required: true
  },
  scaling: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['previous-step', 'next-step'])

// Composables
//const settings = useWorkflowSettings()
//const workflowStore = useWorkflowStore()

// Dynamic font sizes - ONLY changing styling reference
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.caption}px`

// Methods - Only control viewedStep, NOT currentStep!
function goToPreviousStep() {
  if (props.viewedStep > 0) {
    emit('previous-step')
  }
}

function goToNextStep() {
  if (props.viewedStep < props.workflowSteps.length - 1) {
    emit('next-step')
  }
}
</script>

<template>
  <div class="p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-900">
    <div class="flex justify-between items-center">
      <div class="flex space-x-3">
        <button :style="{ fontSize: _body }" class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <i class="fas fa-pause mr-2"></i>
          Pause
        </button>
        <button :style="{ fontSize: _body }" class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-300 dark:border-gray-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
          <i class="fas fa-stop mr-2"></i>
          Cancel
        </button>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="goToPreviousStep"
          :disabled="viewedStep === 0"
          :style="{ fontSize: _body }" 
          class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fas fa-arrow-left mr-2"></i>
          Previous
        </button>
        <button 
          @click="goToNextStep"
          :disabled="viewedStep >= workflowSteps.length - 1"
          :style="{ fontSize: _body }" 
          class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fas fa-arrow-right mr-2"></i>
          Next Step
        </button>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>

