<script setup>
import { computed } from 'vue'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'base',
    validator: (value) => ['xs', 'sm', 'base', 'lg', 'xl'].includes(value)
  },
  color: {
    type: String,
    default: 'current'
  }
})

const { fontSizes } = useScaling()

const iconSize = computed(() => {
  const sizes = {
    xs: fontSizes.xs,
    sm: fontSizes.sm,
    base: fontSizes.base,
    lg: fontSizes.lg,
    xl: fontSizes.xl
  }
  return `${sizes[props.size]}px`
})

const iconClasses = computed(() => {
  const colorClasses = {
    current: 'text-current',
    blue: 'text-blue-500',
    green: 'text-green-500',
    red: 'text-red-500',
    yellow: 'text-yellow-500',
    purple: 'text-purple-500',
    gray: 'text-gray-500'
  }
  
  return colorClasses[props.color] || colorClasses.current
})
</script>

<template>
  <i 
    :class="[props.icon, iconClasses]" 
    :style="{ fontSize: iconSize }"
  ></i>
</template>
