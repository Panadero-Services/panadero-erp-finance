<script setup>
import { computed } from 'vue';

const props = defineProps({
  // Button variants
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'primary', 'success', 'danger', 'warning', 'info'].includes(value)
  },
  // Button sizes
  size: {
    type: String,
    default: 'xs',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  // Button types
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },
  // Loading state
  loading: {
    type: Boolean,
    default: false
  },
  // Icon only button
  iconOnly: {
    type: Boolean,
    default: false
  },
  // Full width button
  fullWidth: {
    type: Boolean,
    default: false
  },
  // Rounded button
  rounded: {
    type: Boolean,
    default: false
  },
  // Hover effect
  hover: {
    type: String,
    default: 'scale',
    validator: (value) => ['scale', 'lift', 'glow', 'none'].includes(value)
  }
});

const emit = defineEmits(['click']);

const buttonClasses = computed(() => {
  const baseClasses = [
    'inline-flex items-center justify-center font-medium transition-all duration-200',
    'bg-transparent backdrop-blur-sm',
    props.fullWidth ? 'w-full' : '',
    props.rounded ? 'rounded-full' : 'rounded-md',
    props.disabled || props.loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
  ];

  // Size classes
  const sizeClasses = {
    xs: 'px-2 py-1 text-xs',
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-5 py-2.5 text-base',
    xl: 'px-6 py-3 text-lg'
  };

  // Variant classes
  const variantClasses = {
    default: 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50',
    primary: 'text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20',
    success: 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20',
    danger: 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20',
    warning: 'text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20',
    info: 'text-cyan-600 dark:text-cyan-400 hover:bg-cyan-50 dark:hover:bg-cyan-900/20'
  };

  // Hover effect classes
  const hoverClasses = {
    scale: 'hover:scale-105',
    lift: 'hover:-translate-y-0.5',
    glow: 'hover:shadow-lg hover:shadow-current/20',
    none: ''
  };

  return [
    ...baseClasses,
    sizeClasses[props.size],
    variantClasses[props.variant],
    hoverClasses[props.hover],
    props.iconOnly ? 'p-2' : ''
  ].join(' ');
});

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event);
  }
};
</script>

<template>
  <button
    :type="type"
    :class="buttonClasses"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <!-- Loading spinner -->
    <svg
      v-if="loading"
      class="animate-spin -ml-1 mr-2 h-4 w-4"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>

    <!-- Button content -->
    <slot></slot>
  </button>
</template> 