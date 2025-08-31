<!--
  Modal Footer Component
  @version 1.1.0
  @description Footer for workflow modals with action buttons
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  workflow: {
    type: Object,
    required: true
  },
  workflowStore: {
    type: Object,
    required: true
  },
  currentStep: {
    type: Number,
    default: 0
  },
  canProceed: {
    type: Boolean,
    default: true
  },
  canGoBack: {
    type: Boolean,
    default: true
  }
})

// Emits
const emit = defineEmits(['close', 'next', 'previous', 'complete'])

// Settings
const settings = useWorkflowSettings()

// Computed properties
const isLastStep = computed(() => {
  return props.currentStep >= (props.workflow.steps?.length - 1)
})

const stepName = computed(() => {
  const step = props.workflow.steps?.[props.currentStep]
  return step?.name || `Step ${props.currentStep + 1}`
})

// Dynamic font sizes
const _body = computed(() => `${settings.fontSizesComputed.value.body}px`)
const _caption = computed(() => `${settings.fontSizesComputed.value.caption}px`)

// Get color classes from store
const getColorClasses = (colorType, element) => {
  return props.workflowStore.getColorClasses(colorType, element)
}
</script>

<template>
  <div class="flex justify-between items-center p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
    <!-- Left side - Step info -->
    <div class="flex items-center space-x-3">
      <span :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">
        Step {{ props.currentStep + 1 }} of {{ workflow.steps?.length || 0 }}
      </span>
      <span :style="{ fontSize: _body }" class="font-medium text-gray-700 dark:text-gray-300">
        {{ stepName }}
      </span>
    </div>
    
    <!-- Right side - Action buttons -->
    <div class="flex items-center space-x-3">
      <!-- Back button -->
      <button
        v-if="canGoBack && currentStep > 0"
        @click="$emit('previous')"
        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Back
      </button>
      
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Close
      </button>
      
      <!-- Next/Complete button -->
      <button
        v-if="canProceed"
        @click="isLastStep ? $emit('complete') : $emit('next')"
        :class="[
          'px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
          isLastStep 
            ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' 
            : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
        ]">
        {{ isLastStep ? 'Complete' : 'Next' }}
      </button>
    </div>
  </div>
</template>