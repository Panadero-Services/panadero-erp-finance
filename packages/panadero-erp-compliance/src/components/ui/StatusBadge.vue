<script setup>
import { computed } from 'vue'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const props = defineProps({
  status: {
    type: String,
    required: true
  },
  text: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

const { scalingStyles } = useScaling()

const displayText = computed(() => {
  return props.text || props.status
})

const badgeClasses = computed(() => {
  const baseClasses = 'inline-flex items-center font-medium rounded-full'
  
  const statusClasses = {
    'success': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'error': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    'info': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'critical': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'in-progress': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'pending': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'active': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'inactive': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    'draft': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'published': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'open': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    'resolved': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'closed': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
  }
  
  const sizeClasses = {
    sm: 'px-2 py-1 text-xs',
    md: 'px-2.5 py-0.5 text-sm',
    lg: 'px-3 py-1 text-base'
  }
  
  return [
    baseClasses,
    statusClasses[props.status] || statusClasses['info'],
    sizeClasses[props.size]
  ].join(' ')
})
</script>

<template>
  <span :class="badgeClasses" :style="scalingStyles.textFontSize">
    {{ displayText }}
  </span>
</template>