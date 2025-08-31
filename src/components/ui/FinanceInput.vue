<script setup>
import { computed } from 'vue';
//import { useFinanceStore } from '../../stores/financeStore';
import { useScaling } from '../../../../shared/composables/useScaling.js'
const { fontSizes, scalingStyles, spacing } = useScaling()

// Props
const props = defineProps({
  // Input value
  modelValue: {
    type: [String, Number],
    default: ''
  },
  
  // Input type
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'email', 'password', 'number', 'tel', 'url', 'search'].includes(value)
  },
  
  // Placeholder text
  placeholder: {
    type: String,
    default: ''
  },
  
  // Size variants to match FinanceButton and FinanceDropdown
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['small', 'normal', 'large'].includes(value)
  },
  
  // Variant/theme to match FinanceDropdown
  variant: {
    type: String,
    default: 'outline',
    validator: (value) => [
      'outline',
      'filled',
      'ghost'
    ].includes(value)
  },
  
  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },
  
  // Required state
  required: {
    type: Boolean,
    default: false
  },
  
  // Full width
  fullWidth: {
    type: Boolean,
    default: false
  },
  
  // Icon (left side)
  iconLeft: {
    type: String,
    default: ''
  },
  
  // Icon (right side)
  iconRight: {
    type: String,
    default: ''
  },
  
  // Label text
  label: {
    type: String,
    default: ''
  },
  
  // Error message
  error: {
    type: String,
    default: ''
  },
  
  // Help text
  helpText: {
    type: String,
    default: ''
  }
});

// Emits
const emit = defineEmits(['update:modelValue', 'input', 'change', 'focus', 'blur']);

// Store and scaling
//const store = useFinanceStore();

// Computed dynamic styles
const dynamicStyles = computed(() => ({
  fontSize: `${fontSizes.base}px`,
  ...scalingStyles.buttonPadding
}));

// Computed classes
const variantClasses = {
  outline: 'finance-input--outline',
  filled: 'finance-input--filled',
  ghost: 'finance-input--ghost'
};

const sizeClasses = {
  small: {
    container: 'finance-input--small',
    icon: 'h-3 w-3'
  },
  normal: {
    container: 'finance-input--normal',
    icon: 'h-4 w-4'
  },
  large: {
    container: 'finance-input--large',
    icon: 'h-5 w-5'
  }
};

// Input classes
const inputClasses = computed(() => [
  'finance-input',
  variantClasses[props.variant],
  sizeClasses[props.size].container,
  {
    'finance-input--disabled': props.disabled,
    'finance-input--full-width': props.fullWidth,
    'finance-input--error': props.error,
    'finance-input--with-icon-left': props.iconLeft,
    'finance-input--with-icon-right': props.iconRight
  }
]);

// Event handlers
const handleInput = (event) => {
  const value = event.target.value;
  emit('update:modelValue', value);
  emit('input', value);
};

const handleChange = (event) => {
  emit('change', event.target.value);
};

const handleFocus = (event) => {
  emit('focus', event);
};

const handleBlur = (event) => {
  emit('blur', event);
};
</script>

<template>
  <div class="finance-input-wrapper" :class="{ 'w-full': fullWidth }">
    <!-- Label -->
    <label 
      v-if="label" 
      class="block text-sm font-medium mb-1"
    :style="[scalingStyles.textFontSize, scalingStyles.buttonPadding]" 
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative" :class="{ 'w-full': fullWidth }">
      <!-- Left Icon -->
      <div 
        v-if="iconLeft" 
        class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
      >
        <i :class="[iconLeft, sizeClasses[size].icon, 'text-gray-400']"></i>
      </div>

      <!-- Input Field -->
      <input
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :class="inputClasses"
    :style="[scalingStyles.textFontSize, scalingStyles.buttonPadding]" 
        @input="handleInput"
        @change="handleChange"
        @focus="handleFocus"
        @blur="handleBlur"
      />

      <!-- Right Icon -->
      <div 
        v-if="iconRight" 
        class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
      >
        <i :class="[iconRight, sizeClasses[size].icon, 'text-gray-400']"></i>
      </div>
    </div>

    <!-- Help Text -->
    <p 
      v-if="helpText && !error" 
      class="mt-1 text-gray-500 dark:text-gray-400"
    :style="[scalingStyles.textFontSize, scalingStyles.buttonPadding]" 
    >
      {{ helpText }}
    </p>

    <!-- Error Message -->
    <p 
      v-if="error" 
      class="mt-1 text-red-500 dark:text-red-400"
    :style="[scalingStyles.textFontSize, scalingStyles.buttonPadding]" 
    >
      {{ error }}
    </p>
  </div>
</template>

<style scoped>
/* Base input styles */
.finance-input {
  @apply font-medium rounded-lg transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
  width: auto !important;
  display: inline-block !important;
}

/* Size variants - matching FinanceDropdown exactly */
.finance-input--small {
  @apply px-3 py-2 text-xs;
  min-height: 32px !important;
  height: auto !important;
  width: auto !important;
}

.finance-input--normal {
  @apply px-4 py-2.5 text-sm;
  min-height: 40px !important;
  height: auto !important;
  width: auto !important;
}

.finance-input--large {
  @apply px-6 py-3 text-base;
  min-height: 48px !important;
  height: auto !important;
  width: auto !important;
}

/* Icon spacing adjustments */
.finance-input--with-icon-left.finance-input--small {
  padding-left: 2.25rem !important;
}

.finance-input--with-icon-left.finance-input--normal {
  padding-left: 2.5rem !important;
}

.finance-input--with-icon-left.finance-input--large {
  padding-left: 3rem !important;
}

.finance-input--with-icon-right.finance-input--small {
  padding-right: 2.25rem !important;
}

.finance-input--with-icon-right.finance-input--normal {
  padding-right: 2.5rem !important;
}

.finance-input--with-icon-right.finance-input--large {
  padding-right: 3rem !important;
}

/* Variant styles - matching FinanceDropdown exactly */
.finance-input--outline {
  background-color: transparent !important;
  border: 2px solid #6366f1 !important;
  color: #6366f1 !important;
}

.finance-input--outline::placeholder {
  color: rgba(99, 102, 241, 0.6) !important;
}

.finance-input--outline:hover {
  background-color: rgba(99, 102, 241, 0.05) !important;
  border-color: #4f46e5 !important;
  color: #4f46e5 !important;
}

.finance-input--outline:focus {
  @apply ring-indigo-500;
  border-color: #4f46e5 !important;
}

.dark .finance-input--outline {
  border-color: #60a5fa !important;
  color: #60a5fa !important;
}

.dark .finance-input--outline::placeholder {
  color: rgba(96, 165, 250, 0.6) !important;
}

.dark .finance-input--outline:hover {
  background-color: rgba(96, 165, 250, 0.05) !important;
  border-color: #3b82f6 !important;
  color: #3b82f6 !important;
}

.finance-input--filled {
  background: linear-gradient(to right, #6366f1, #4f46e5) !important;
  color: white !important;
  border: 2px solid transparent !important;
  box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.25) !important;
}

.finance-input--filled::placeholder {
  color: rgba(255, 255, 255, 0.8) !important;
}

.finance-input--filled:hover {
  background: linear-gradient(to right, #4f46e5, #4338ca) !important;
  box-shadow: 0 6px 8px -2px rgba(99, 102, 241, 0.3) !important;
}

.finance-input--filled:focus {
  @apply ring-indigo-500;
}

.finance-input--ghost {
  background-color: rgba(255, 255, 255, 0.8) !important;
  color: #374151 !important;
  border: 1px solid rgba(229, 231, 235, 0.5) !important;
}

.finance-input--ghost::placeholder {
  color: rgba(55, 65, 81, 0.6) !important;
}

.finance-input--ghost:hover {
  background-color: rgba(249, 250, 251, 1) !important;
}

.finance-input--ghost:focus {
  @apply ring-gray-500;
}

.dark .finance-input--ghost {
  background-color: rgba(31, 41, 55, 0.8) !important;
  color: #d1d5db !important;
  border: 1px solid rgba(55, 65, 81, 1) !important;
}

.dark .finance-input--ghost::placeholder {
  color: rgba(209, 213, 219, 0.6) !important;
}

.dark .finance-input--ghost:hover {
  background-color: rgba(55, 65, 81, 0.5) !important;
}

/* State styles */
.finance-input--disabled {
  @apply opacity-50 cursor-not-allowed;
}

.finance-input--full-width {
  width: 100% !important;
}

.finance-input--error {
  border-color: #ef4444 !important;
  color: #ef4444 !important;
}

.finance-input--error:focus {
  @apply ring-red-500;
}

/* Dark mode focus ring adjustments */
.dark .finance-input:focus {
  @apply ring-offset-gray-800;
}

/* Wrapper full width */
.finance-input-wrapper.w-full .finance-input {
  width: 100% !important;
}
</style>

