<script setup>
import { computed } from 'vue'
import { useScaling } from 'panadero-shared-styling'

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

const { scalingStyles } = useScaling()

const iconSize = computed(() => {
  const sizes = {
    xs: scalingStyles.iconSizeSmall,
    sm: scalingStyles.iconSizeSmall,
    base: scalingStyles.iconSize,
    lg: scalingStyles.iconSize,
    xl: scalingStyles.iconSizeLarge
  }
  return sizes[props.size] || scalingStyles.iconSize
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
    :style="iconSize"
  ></i>
</template>
