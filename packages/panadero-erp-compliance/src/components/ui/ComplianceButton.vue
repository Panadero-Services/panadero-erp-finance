<script setup>
import { computed } from 'vue'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const { scalingStyles } = useScaling()

const buttonClasses = computed(() => {
  const baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none'
  
  const variantClasses = {
    primary: 'bg-teal-600 hover:bg-teal-700 text-white focus:ring-teal-500 shadow-lg shadow-teal-500/25',
    secondary: 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
    outline: 'border-2 border-teal-600 text-teal-600 hover:bg-teal-600 hover:text-white focus:ring-teal-500',
    ghost: 'text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-900/20 focus:ring-teal-500',
    danger: 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500'
  }
  
  const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-6 py-3 text-lg'
  }
  
  return [
    baseClasses,
    variantClasses[props.variant],
    sizeClasses[props.size]
  ].join(' ')
})

const handleClick = (event) => {
  if (!props.loading && !props.disabled) {
    emit('click', event)
  }
}
</script>

<template>
  <button
    :class="buttonClasses"
    :style="scalingStyles.buttonPadding"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
    <slot v-else />
  </button>
</template>