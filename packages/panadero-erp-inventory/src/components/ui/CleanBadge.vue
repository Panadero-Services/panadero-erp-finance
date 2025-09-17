<!--
  Clean Badge Component
  @version 1.0.12
  @date 16-Sep-2025
  @description Clean, subtle badges without background colors for consistent styling
-->
<script setup>
import { computed } from 'vue'
import { useScaling } from 'panadero-shared-styling'

const { scalingStyles } = useScaling()

const props = defineProps({
  variant: {
    type: String,
    default: 'gray',
    validator: (value) => ['blue', 'green', 'red', 'yellow', 'purple', 'gray', 'orange', 'cyan'].includes(value)
  },
  text: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['xs', 'sm', 'md'].includes(value)
  }
})

const badgeClasses = computed(() => {
  const baseClasses = 'inline-flex items-center font-medium rounded-md border transition-colors'
  
  const variantClasses = {
    blue: 'text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-900/10',
    green: 'text-green-600 dark:text-green-400 border-green-200 dark:border-green-800 bg-green-50/50 dark:bg-green-900/10',
    red: 'text-red-600 dark:text-red-400 border-red-200 dark:border-red-800 bg-red-50/50 dark:bg-red-900/10',
    yellow: 'text-yellow-600 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800 bg-yellow-50/50 dark:bg-yellow-900/10',
    purple: 'text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-900/10',
    gray: 'text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50',
    orange: 'text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800 bg-orange-50/50 dark:bg-orange-900/10',
    cyan: 'text-cyan-600 dark:text-cyan-400 border-cyan-200 dark:border-cyan-800 bg-cyan-50/50 dark:bg-cyan-900/10'
  }
  
  const sizeClasses = {
    xs: 'px-2 py-0.5 text-xs',
    sm: 'px-2.5 py-1 text-sm',
    md: 'px-3 py-1.5 text-base'
  }
  
  return `${baseClasses} ${variantClasses[props.variant]} ${sizeClasses[props.size]}`
})

const badgeStyle = computed(() => {
  const sizeMap = {
    xs: scalingStyles.smallFontSize || { fontSize: '12px' },
    sm: scalingStyles.textFontSize || { fontSize: '14px' },
    md: scalingStyles.subtitleFontSize || { fontSize: '16px' }
  }
  
  return sizeMap[props.size] || scalingStyles.textFontSize || { fontSize: '14px' }
})
</script>

<template>
  <span :class="badgeClasses" :style="badgeStyle">
    {{ text }}
  </span>
</template>
