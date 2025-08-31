<template>
  <i 
    :class="iconClasses"
    :style="iconStyle"
    :title="title"
  ></i>
</template>

<script setup>
import { computed } from 'vue';
import { useFinanceStore } from '../../stores/financeStore';
import { useScaling } from '../../../../../shared/composables/useScaling.js'

const store = useFinanceStore();
const { fontSizes, scalingStyles, spacing } = useScaling()

const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'normal', // small, normal, large, extra-large
    validator: (value) => ['small', 'normal', 'large', 'extra-large'].includes(value)
  },
  color: {
    type: String,
    default: 'default' // default, primary, success, warning, danger, info
  },
  title: {
    type: String,
    default: ''
  }
});

const iconClasses = computed(() => {
  const baseClass = `fas ${props.icon}`;
  
  const colorClasses = {
    default: 'text-gray-600 dark:text-gray-400',
    primary: 'text-indigo-600 dark:text-indigo-400',
    success: 'text-green-600 dark:text-green-400',
    warning: 'text-yellow-600 dark:text-yellow-400',
    danger: 'text-red-600 dark:text-red-400',
    info: 'text-blue-600 dark:text-blue-400'
  };
  
  return `${baseClass} ${colorClasses[props.color]}`;
});

const iconStyle = computed(() => {
  const sizeMap = {
    small: scalingStyles.iconSizeSmall,
    normal: scalingStyles.iconSize,
    large: scalingStyles.iconSizeLarge,
    'extra-large': scalingStyles.iconSizeExtraLarge
  };
  
  return sizeMap[props.size];
});
</script>
