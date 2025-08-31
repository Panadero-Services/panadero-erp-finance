<script setup>
import { useScaling } from '../../../../shared/composables/useScaling.js'
const { fontSizes, scalingStyles, spacing } = useScaling()

const props = defineProps({
  status: {
    type: String,
    required: true
  },
  variant: {
    type: String,
    default: 'default', // default, success, warning, danger, info
    validator: (value) => ['default', 'success', 'warning', 'danger', 'info'].includes(value)
  }
});

// Centralized status classes for all finance components
const getStatusClasses = (status, variant) => {
  // If variant is specified, use it
  if (variant !== 'default') {
    const variantClasses = {
      success: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
      warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
      danger: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
      info: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    };
    return variantClasses[variant];
  }

  // Auto-detect status based on common patterns
  const statusLower = status.toLowerCase();
  
  // Invoice/Receivable/Payable statuses
  if (statusLower.includes('draft') || statusLower.includes('pending')) {
    return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
  }
  if (statusLower.includes('approved') || statusLower.includes('posted') || statusLower.includes('active')) {
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
  }
  if (statusLower.includes('paid') || statusLower.includes('completed') || statusLower.includes('closed')) {
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
  }
  if (statusLower.includes('overdue') || statusLower.includes('failed') || statusLower.includes('cancelled')) {
    return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
  }
  if (statusLower.includes('partial') || statusLower.includes('processing')) {
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
  }

  // Cash Flow types
  if (statusLower.includes('inflow')) {
    return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200';
  }
  if (statusLower.includes('outflow')) {
    return 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200';
  }

  // Audit/Compliance statuses
  if (statusLower.includes('passed') || statusLower.includes('compliant')) {
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
  }
  if (statusLower.includes('failed') || statusLower.includes('non-compliant')) {
    return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
  }
  if (statusLower.includes('pending') || statusLower.includes('review')) {
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
  }

  // Budget statuses
  if (statusLower.includes('under') || statusLower.includes('within')) {
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
  }
  if (statusLower.includes('over') || statusLower.includes('exceeded')) {
    return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
  }

  // Default fallback
  return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};
</script>
<template>
  <span 
    :style="[scalingStyles.textFontSize, scalingStyles.borderRadius]" 
    :class="`${getStatusClasses(status, variant)} px-2 py-1 rounded font-medium`"
  >
    {{ status }}
  </span>
</template>
