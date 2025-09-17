<!--
  Action Button Component
  @version 1.0.12
  @date 16-Sep-2025
  @description Clean, subtle action buttons for edit, delete, view operations
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'edit',
    validator: (value) => ['edit', 'delete', 'view', 'info', 'success', 'warning'].includes(value)
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['xs', 'sm', 'md'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  iconStyle: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
  const baseClasses = 'inline-flex items-center justify-center rounded-md transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed'
  
  const variantClasses = {
    edit: 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10 focus:ring-blue-500',
    delete: 'text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/10 focus:ring-red-500',
    view: 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900/10 focus:ring-gray-500',
    info: 'text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-cyan-900/10 focus:ring-cyan-500',
    success: 'text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/10 focus:ring-green-500',
    warning: 'text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/10 focus:ring-yellow-500'
  }
  
  const sizeClasses = {
    xs: 'p-1.5',
    sm: 'p-2',
    md: 'p-2.5'
  }
  
  return `${baseClasses} ${variantClasses[props.variant]} ${sizeClasses[props.size]}`
})

const iconClasses = computed(() => {
  const iconMap = {
    edit: 'fas fa-edit',
    delete: 'fas fa-trash',
    view: 'fas fa-eye',
    info: 'fas fa-info-circle',
    success: 'fas fa-check',
    warning: 'fas fa-exclamation-triangle'
  }
  
  return iconMap[props.variant] || iconMap.edit
})

const iconSize = computed(() => {
  // If parent passed iconStyle, use it (reactive)
  if (props.iconStyle) {
    return props.iconStyle
  }
  
  // Fallback to size-based defaults (static)
  const sizeMap = {
    xs: { fontSize: '12px' },
    sm: { fontSize: '14px' },
    md: { fontSize: '16px' }
  }
  
  return sizeMap[props.size] || { fontSize: '14px' }
})

const buttonTitle = computed(() => {
  if (props.title) return props.title
  
  const titleMap = {
    edit: 'Edit',
    delete: 'Delete',
    view: 'View',
    info: 'Info',
    success: 'Success',
    warning: 'Warning'
  }
  
  return titleMap[props.variant] || 'Action'
})

const handleClick = (event) => {
  if (!props.disabled) {
    emit('click', event)
  }
}
</script>

<template>
  <button
    :class="buttonClasses"
    :disabled="disabled"
    :title="buttonTitle"
    @click="handleClick"
  >
    <i :class="iconClasses" :style="iconSize"></i>
  </button>
</template>