<script setup>
import { computed, ref } from 'vue';
import { useFinanceStore } from '../../stores/financeStore';

import { useScaling } from '../../../../shared/composables/useScaling.js'
const { fontSizes, scalingStyles, spacing } = useScaling()

// Props
const props = defineProps({
  // Dropdown options
  options: {
    type: Array,
    default: () => [],
    validator: (value) => Array.isArray(value)
  },
  
  // Selected value
  modelValue: {
    type: [String, Number, Object],
    default: null
  },
  
  // Placeholder text
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  
  // Size variants to match FinanceButton
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['small', 'normal', 'large'].includes(value)
  },
  
  // Variant/theme to match FinanceButton
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
  
  // Full width
  fullWidth: {
    type: Boolean,
    default: false
  },
  
  // Option display key
  optionLabel: {
    type: String,
    default: 'label'
  },
  
  // Option value key
  optionValue: {
    type: String,
    default: 'value'
  }
});

// Emits
const emit = defineEmits(['update:modelValue', 'change']);

// Store
const store = useFinanceStore();

// Local state
const isOpen = ref(false);
const dropdownRef = ref(null);

// Computed dynamic styles
const dynamicStyles = computed(() => ({
  fontSize: `${fontSizes.base}px`,
  ...scalingStyles.buttonPadding
}));

// Computed classes
const variantClasses = {
  outline: 'finance-dropdown--outline',
  filled: 'finance-dropdown--filled',
  ghost: 'finance-dropdown--ghost'
};

const sizeClasses = {
  small: {
    container: 'finance-dropdown--small',
    icon: 'h-3 w-3'
  },
  normal: {
    container: 'finance-dropdown--normal',
    icon: 'h-4 w-4'
  },
  large: {
    container: 'finance-dropdown--large',
    icon: 'h-5 w-5'
  }
};

// Computed selected option display
const selectedOptionDisplay = computed(() => {
  if (!props.modelValue) return props.placeholder;
  
  const option = props.options.find(opt => {
    if (typeof opt === 'string') return opt === props.modelValue;
    return opt[props.optionValue] === props.modelValue;
  });
  
  if (!option) return props.placeholder;
  
  if (typeof option === 'string') return option;
  return option[props.optionLabel] || option[props.optionValue];
});

// Methods
const toggleDropdown = () => {
  if (!props.disabled) {
    isOpen.value = !isOpen.value;
  }
};

const selectOption = (option) => {
  const value = typeof option === 'string' ? option : option[props.optionValue];
  emit('update:modelValue', value);
  emit('change', value, option);
  isOpen.value = false;
};

const closeDropdown = () => {
  isOpen.value = false;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown();
  }
};

// Add/remove click outside listener
const addClickOutsideListener = () => {
  document.addEventListener('click', handleClickOutside);
};

const removeClickOutsideListener = () => {
  document.removeEventListener('click', handleClickOutside);
};

// Watch isOpen to manage listeners
import { watch, onBeforeUnmount } from 'vue';

watch(isOpen, (newValue) => {
  if (newValue) {
    addClickOutsideListener();
  } else {
    removeClickOutsideListener();
  }
});

onBeforeUnmount(() => {
  removeClickOutsideListener();
});
</script>

<template>
  <div 
    ref="dropdownRef"
    class="relative"
    :class="{ 'w-full': fullWidth }"
  >
    <!-- Dropdown Button -->
    <button
      type="button"
      :disabled="disabled"
      :style="[scalingStyles.textFontSize, scalingStyles.buttonPadding]" 
      :class="[
        'finance-dropdown group',
        variantClasses[variant],
        sizeClasses[size].container,
        {
          'finance-dropdown--disabled': disabled,
          'finance-dropdown--full-width': fullWidth,
          'finance-dropdown--open': isOpen
        }
      ]"
      @click="toggleDropdown"
    >
      <!-- Selected option text -->
      <span class="flex-1 text-left truncate">
        {{ selectedOptionDisplay }}
      </span>
      
      <!-- Chevron icon -->
      <i 
        :class="[
          'fas fa-chevron-down transition-transform duration-200',
          sizeClasses[size].icon,
          {
            'rotate-180': isOpen
          }
        ]"
      ></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div
      v-if="isOpen"
      class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <div
        v-for="(option, index) in options"
        :key="index"
        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
        :style="{ fontSize: `${fontSizes.base}px` }"
        @click="selectOption(option)"
      >
        <span class="block truncate">
          {{ typeof option === 'string' ? option : option[optionLabel] }}
        </span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Base dropdown styles */
.finance-dropdown {
  @apply inline-flex items-center justify-between font-medium rounded-lg transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
  width: auto !important;
  display: inline-flex !important;
}

/* Size variants - matching FinanceButton */
.finance-dropdown--small {
  @apply px-3 py-2 text-xs;
  min-height: 32px !important;
  height: auto !important;
  width: auto !important;
}

.finance-dropdown--normal {
  @apply px-4 py-2.5 text-sm;
  min-height: 40px !important;
  height: auto !important;
  width: auto !important;
}

.finance-dropdown--large {
  @apply px-6 py-3 text-base;
  min-height: 48px !important;
  height: auto !important;
  width: auto !important;
}

/* Variant styles */
.finance-dropdown--outline {
  background-color: transparent !important;
  border: 2px solid #6366f1 !important;
  color: #6366f1 !important;
}

.finance-dropdown--outline:hover {
  background-color: rgba(99, 102, 241, 0.05) !important;
  border-color: #4f46e5 !important;
  color: #4f46e5 !important;
}

.finance-dropdown--outline:focus {
  @apply ring-indigo-500;
}

.dark .finance-dropdown--outline {
  border-color: #60a5fa !important;
  color: #60a5fa !important;
}

.dark .finance-dropdown--outline:hover {
  background-color: rgba(96, 165, 250, 0.05) !important;
  border-color: #3b82f6 !important;
  color: #3b82f6 !important;
}

.finance-dropdown--filled {
  background: linear-gradient(to right, #6366f1, #4f46e5) !important;
  color: white !important;
  border: 2px solid transparent !important;
  box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.25) !important;
}

.finance-dropdown--filled:hover {
  background: linear-gradient(to right, #4f46e5, #4338ca) !important;
  box-shadow: 0 6px 8px -2px rgba(99, 102, 241, 0.3) !important;
}

.finance-dropdown--ghost {
  background-color: rgba(255, 255, 255, 0.8) !important;
  color: #374151 !important;
  border: 1px solid rgba(229, 231, 235, 0.5) !important;
}

.finance-dropdown--ghost:hover {
  background-color: rgba(249, 250, 251, 1) !important;
}

.dark .finance-dropdown--ghost {
  background-color: rgba(31, 41, 55, 0.8) !important;
  color: #d1d5db !important;
  border: 1px solid rgba(55, 65, 81, 1) !important;
}

.dark .finance-dropdown--ghost:hover {
  background-color: rgba(55, 65, 81, 0.5) !important;
}

/* State styles */
.finance-dropdown--disabled {
  @apply opacity-50 cursor-not-allowed;
}

.finance-dropdown--full-width {
  @apply w-full;
  width: 100% !important;
}

.finance-dropdown--open {
  @apply ring-2 ring-offset-2;
}

.finance-dropdown--outline.finance-dropdown--open {
  @apply ring-indigo-500;
}

/* Dark mode focus ring adjustments */
.dark .finance-dropdown:focus {
  @apply ring-offset-gray-800;
}

/* Dropdown menu animations */
.finance-dropdown + div {
  animation: slideDown 0.15s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
