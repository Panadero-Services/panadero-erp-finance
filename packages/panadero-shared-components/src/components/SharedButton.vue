<script setup>
import { computed } from 'vue';
import { useScaling } from 'panadero-shared-styling'

const { fontSizes, scalingStyles, spacing } = useScaling()

// Props
const props = defineProps({
  // Button type
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  
  // Button variant/theme
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => [
      'primary',
      'secondary',
      'success',
      'danger',
      'warning',
      'info',
      'ghost',
      'outline',
      'outline-primary',
      'outline-secondary',
      'outline-success',
      'outline-danger',
      'outline-warning',
      'outline-info'
    ].includes(value)
  },
  
  // Button size
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['small', 'normal', 'large'].includes(value)
  },
  
  // Button state
  disabled: {
    type: Boolean,
    default: false
  },
  
  loading: {
    type: Boolean,
    default: false
  },
  
  // Layout
  fullWidth: {
    type: Boolean,
    default: false
  },
  
  // Icons
  iconLeft: {
    type: [String, Object],
    default: null
  },
  
  iconRight: {
    type: [String, Object],
    default: null
  }
});

// Emits
const emit = defineEmits(['click']);

// Computed classes
const variantClasses = {
  primary: 'shared-button--primary',
  secondary: 'shared-button--secondary',
  success: 'shared-button--success',
  danger: 'shared-button--danger',
  warning: 'shared-button--warning',
  info: 'shared-button--info',
  ghost: 'shared-button--ghost',
  outline: 'shared-button--outline',
  'outline-primary': 'shared-button--outline-primary',
  'outline-secondary': 'shared-button--outline-secondary',
  'outline-success': 'shared-button--outline-success',
  'outline-danger': 'shared-button--outline-danger',
  'outline-warning': 'shared-button--outline-warning',
  'outline-info': 'shared-button--outline-info'
};

const sizeClasses = {
  small: {
    container: 'shared-button--small',
    icon: 'shared-button__icon--small'
  },
  normal: {
    container: 'shared-button--normal',
    icon: 'shared-button__icon--normal'
  },
  large: {
    container: 'shared-button--large',
    icon: 'shared-button__icon--large'
  }
};

// Event handlers
const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event);
  }
};
</script>

<template>
  <button
    :type="type"
    :disabled="disabled"
    :style="[scalingStyles.textFontSize, scalingStyles.buttonPadding]" 
    :class="[
      'shared-button group',
      variantClasses[variant],
      sizeClasses[size].container,
      {
        'shared-button--disabled': disabled,
        'shared-button--loading': loading,
        'shared-button--full-width': fullWidth
      }
    ]"
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
    
    <!-- Icon (left) -->
    <i
      v-if="iconLeft"
      :class="[
        iconLeft,
        'shared-button__icon',
        'shared-button__icon--left',
        sizeClasses[size].icon
      ]"
    ></i>
    
    <!-- Content -->
    <span v-if="$slots.default" class="shared-button__content">
      <slot />
    </span>
    
    <!-- Icon (right) -->
    <i
      v-if="iconRight"
      :class="[
        iconRight,
        'shared-button__icon',
        'shared-button__icon--right',
        sizeClasses[size].icon
      ]"
    ></i>
  </button>
</template>

<style scoped>
/* Base button styles */
.shared-button {
  @apply inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
  width: auto !important;
  display: inline-flex !important;
}

/* Size variants */
.shared-button--small {
  @apply px-3 py-2 text-xs;
  min-height: 32px !important;
  height: auto !important;
  width: auto !important;
}

.shared-button--normal {
  @apply px-4 py-2.5 text-sm;
  min-height: 40px !important;
  height: auto !important;
  width: auto !important;
}

.shared-button--large {
  @apply px-6 py-3 text-base;
  min-height: 48px !important;
  height: auto !important;
  width: auto !important;
}

/* Variant styles */
.shared-button--primary {
  background: linear-gradient(to right, #6366f1, #4f46e5) !important;
  color: white !important;
  box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.25), 0 4px 6px -2px rgba(99, 102, 241, 0.25) !important;
}

.shared-button--primary:hover {
  background: linear-gradient(to right, #4f46e5, #4338ca) !important;
  box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.3), 0 10px 10px -5px rgba(99, 102, 241, 0.3) !important;
}

.shared-button--secondary {
  background: linear-gradient(to right, #6b7280, #4b5563) !important;
  color: white !important;
  box-shadow: 0 10px 15px -3px rgba(107, 114, 128, 0.25), 0 4px 6px -2px rgba(107, 114, 128, 0.25) !important;
}

.shared-button--secondary:hover {
  background: linear-gradient(to right, #4b5563, #374151) !important;
  box-shadow: 0 20px 25px -5px rgba(107, 114, 128, 0.3), 0 10px 10px -5px rgba(107, 114, 128, 0.3) !important;
}

.shared-button--success {
  background: linear-gradient(to right, #10b981, #059669) !important;
  color: white !important;
  box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.25), 0 4px 6px -2px rgba(16, 185, 129, 0.25) !important;
}

.shared-button--success:hover {
  background: linear-gradient(to right, #059669, #047857) !important;
  box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.3), 0 10px 10px -5px rgba(16, 185, 129, 0.3) !important;
}

.shared-button--danger {
  background: linear-gradient(to right, #ef4444, #dc2626) !important;
  color: white !important;
  box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.25), 0 4px 6px -2px rgba(239, 68, 68, 0.25) !important;
}

.shared-button--danger:hover {
  background: linear-gradient(to right, #dc2626, #b91c1c) !important;
  box-shadow: 0 20px 25px -5px rgba(239, 68, 68, 0.3), 0 10px 10px -5px rgba(239, 68, 68, 0.3) !important;
}

.shared-button--warning {
  background: linear-gradient(to right, #f59e0b, #d97706) !important;
  color: white !important;
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.25), 0 4px 6px -2px rgba(245, 158, 11, 0.25) !important;
}

.shared-button--warning:hover {
  background: linear-gradient(to right, #d97706, #b45309) !important;
  box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.3), 0 10px 10px -5px rgba(245, 158, 11, 0.3) !important;
}

.shared-button--info {
  background: linear-gradient(to right, #3b82f6, #2563eb) !important;
  color: white !important;
  box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.25), 0 4px 6px -2px rgba(59, 130, 246, 0.25) !important;
}

.shared-button--info:hover {
  background: linear-gradient(to right, #2563eb, #1d4ed8) !important;
  box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.3), 0 10px 10px -5px rgba(59, 130, 246, 0.3) !important;
}

.shared-button--ghost {
  background-color: rgba(255, 255, 255, 0.8) !important;
  color: #374151 !important;
  border: 1px solid rgba(229, 231, 235, 0.5) !important;
}

.shared-button--ghost:hover {
  background-color: rgba(249, 250, 251, 1) !important;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.dark .shared-button--ghost {
  background-color: rgba(31, 41, 55, 0.8) !important;
  color: #d1d5db !important;
  border: 1px solid rgba(55, 65, 81, 1) !important;
}

.dark .shared-button--ghost:hover {
  background-color: rgba(55, 65, 81, 0.5) !important;
}

/* Outline variants with transparent backgrounds and colored borders */
.shared-button--outline {
  background-color: transparent !important;
  border: 2px solid #6366f1 !important;
  color: #6366f1 !important;
}

.shared-button--outline:hover {
  background-color: rgba(99, 102, 241, 0.1) !important;
  border-color: #4f46e5 !important;
  color: #4f46e5 !important;
  box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3), 0 2px 4px -1px rgba(99, 102, 241, 0.3) !important;
}

.dark .shared-button--outline {
  background-color: transparent !important;
  border: 2px solid #60a5fa !important;
  color: #60a5fa !important;
}

.dark .shared-button--outline:hover {
  background-color: rgba(96, 165, 250, 0.1) !important;
  border-color: #3b82f6 !important;
  color: #3b82f6 !important;
}

/* Outline variants for each color */
.shared-button--outline-primary {
  background-color: transparent !important;
  border: 2px solid #6366f1 !important;
  color: #6366f1 !important;
}

.shared-button--outline-primary:hover {
  background-color: rgba(99, 102, 241, 0.1) !important;
  border-color: #4f46e5 !important;
  color: #4f46e5 !important;
}

.shared-button--outline-secondary {
  background-color: transparent !important;
  border: 2px solid #6b7280 !important;
  color: #6b7280 !important;
}

.shared-button--outline-secondary:hover {
  background-color: rgba(107, 114, 128, 0.1) !important;
  border-color: #4b5563 !important;
  color: #4b5563 !important;
}

.shared-button--outline-success {
  background-color: transparent !important;
  border: 2px solid #10b981 !important;
  color: #10b981 !important;
}

.shared-button--outline-success:hover {
  background-color: rgba(16, 185, 129, 0.1) !important;
  border-color: #059669 !important;
  color: #059669 !important;
}

.shared-button--outline-danger {
  background-color: transparent !important;
  border: 2px solid #ef4444 !important;
  color: #ef4444 !important;
}

.shared-button--outline-danger:hover {
  background-color: rgba(239, 68, 68, 0.1) !important;
  border-color: #dc2626 !important;
  color: #dc2626 !important;
}

.shared-button--outline-warning {
  background-color: transparent !important;
  border: 2px solid #f59e0b !important;
  color: #f59e0b !important;
}

.shared-button--outline-warning:hover {
  background-color: rgba(245, 158, 11, 0.1) !important;
  border-color: #d97706 !important;
  color: #d97706 !important;
}

.shared-button--outline-info {
  background-color: transparent !important;
  border: 2px solid #3b82f6 !important;
  color: #3b82f6 !important;
}

.shared-button--outline-info:hover {
  background-color: rgba(59, 130, 246, 0.1) !important;
  border-color: #2563eb !important;
  color: #2563eb !important;
}

/* State styles */
.shared-button--disabled {
  @apply opacity-50 cursor-not-allowed;
}

.shared-button--loading {
  @apply cursor-wait;
}

.shared-button--full-width {
  @apply w-full;
  width: 100% !important;
}

/* Icon styles */
.shared-button__icon {
  @apply flex-shrink-0 transition-transform duration-200 group-hover:scale-110 drop-shadow-sm;
}

.shared-button__icon--left {
  @apply mr-2;
}

.shared-button__icon--right {
  @apply ml-2;
}

.shared-button__icon--small {
  @apply h-3 w-3;
}

.shared-button__icon--normal {
  @apply h-4 w-4;
}

.shared-button__icon--large {
  @apply h-5 w-5;
}

/* Content */
.shared-button__content {
  @apply flex items-center;
}

/* Dark mode focus ring adjustments */
.dark .shared-button:focus {
  @apply ring-offset-gray-800;
}
</style>
