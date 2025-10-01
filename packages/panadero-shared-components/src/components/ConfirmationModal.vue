<!--
  Professional Confirmation Modal
  @version 1.0.0
  @description Professional confirmation dialog with better styling
-->
<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirm Action'
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  variant: {
    type: String,
    default: 'danger',
    validator: (value) => ['danger', 'warning', 'info'].includes(value)
  }
})

const emit = defineEmits(['confirm', 'cancel', 'close'])

const iconClasses = computed(() => {
  const variants = {
    danger: 'text-red-600 dark:text-red-400',
    warning: 'text-yellow-600 dark:text-yellow-400',
    info: 'text-blue-600 dark:text-blue-400'
  }
  return variants[props.variant]
})

const buttonClasses = computed(() => {
  const variants = {
    danger: 'bg-red-600 hover:bg-red-700 text-white',
    warning: 'bg-yellow-600 hover:bg-yellow-700 text-white',
    info: 'bg-blue-600 hover:bg-blue-700 text-white'
  }
  return variants[props.variant]
})

const handleConfirm = () => {
  emit('confirm')
  emit('close')
}

const handleCancel = () => {
  emit('cancel')
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div :class="iconClasses" class="flex-shrink-0 mr-3">
            <i v-if="variant === 'danger'" class="fas fa-exclamation-triangle text-2xl"></i>
            <i v-else-if="variant === 'warning'" class="fas fa-exclamation-circle text-2xl"></i>
            <i v-else class="fas fa-info-circle text-2xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ title }}
          </h3>
        </div>
        
        <div class="mb-6">
          <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
            {{ message }}
          </p>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button
            @click="handleCancel"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors"
          >
            {{ cancelText }}
          </button>
          <button
            @click="handleConfirm"
            :class="buttonClasses"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
