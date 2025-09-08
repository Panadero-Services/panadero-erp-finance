<script setup>
import { computed } from 'vue';
import { useScaling } from '../../../../shared/composables/useScaling.js';

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['active', 'inactive', 'pending', 'completed', 'cancelled', 'success', 'warning', 'error', 'info'].includes(value)
  },
  text: {
    type: String,
    default: ''
  }
});

const { fontSizes } = useScaling();

const badgeClasses = computed(() => {
  const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
  
  const statusClasses = {
    active: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    success: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    error: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    info: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
  };
  
  return `${baseClasses} ${statusClasses[props.status] || statusClasses.inactive}`;
});

const displayText = computed(() => {
  return props.text || props.status.charAt(0).toUpperCase() + props.status.slice(1);
});
</script>

<template>
  <span :class="badgeClasses" :style="{ fontSize: `${fontSizes.xxs}` }">
    {{ displayText }}
  </span>
</template>
