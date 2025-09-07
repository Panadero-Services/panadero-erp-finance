<template>
  <button
    type="button"
    @click="$emit('update:modelValue', !modelValue)"
    :class="[
      'toggle-button',
      sizeClasses[size].container,
      modelValue ? activeClasses : inactiveClasses
    ]"
  >
    <span class="sr-only">{{ label }}</span>
    <div 
      :class="[
        'toggle-slider',
        sizeClasses[size].sliderClass,
        modelValue ? 'slider-active' : 'slider-inactive'
      ]"
    />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  variant: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'red', 'purple', 'yellow'].includes(value)
  },
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['small', 'normal', 'large'].includes(value)
  }
});

defineEmits(['update:modelValue']);

// Size classes for different toggle sizes
const sizeClasses = {
  small: {
    container: 'toggle-small',
    sliderClass: 'slider-small'
  },
  normal: {
    container: 'toggle-normal',
    sliderClass: 'slider-normal'
  },
  large: {
    container: 'toggle-large',
    sliderClass: 'slider-large'
  }
};

// Variant classes for different colors
const variantClasses = {
  blue: 'focus:ring-blue-500',
  green: 'focus:ring-green-500',
  red: 'focus:ring-red-500',
  purple: 'focus:ring-purple-500',
  yellow: 'focus:ring-yellow-500'
};

// Active and inactive states based on variant
const activeClasses = computed(() => {
  const variantMap = {
    blue: 'toggle-blue',
    green: 'toggle-green',
    red: 'toggle-red',
    purple: 'toggle-purple',
    yellow: 'toggle-yellow'
  };
  return variantMap[props.variant];
});

const inactiveClasses = computed(() => {
  return 'toggle-gray';
});
</script>

<style scoped>
.toggle-button {
  position: relative;
  border: none;
  border-radius: 9999px;
  cursor: pointer;
  transition: background-color 0.2s ease-in-out;
  outline: none;
}

.toggle-button:focus {
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Small toggle */
.toggle-small {
  width: 28px;
  height: 16px;
}

/* Normal toggle */
.toggle-normal {
  width: 44px;
  height: 24px;
}

/* Large toggle */
.toggle-large {
  width: 56px;
  height: 32px;
}

/* Toggle colors */
.toggle-blue {
  background-color: #3b82f6;
}

.toggle-green {
  background-color: #10b981;
}

.toggle-red {
  background-color: #ef4444;
}

.toggle-purple {
  background-color: #8b5cf6;
}

.toggle-yellow {
  background-color: #f59e0b;
}

.toggle-gray {
  background-color: #d1d5db;
}

/* Slider styles */
.toggle-slider {
  position: absolute;
  border-radius: 50%;
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

/* Small slider */
.slider-small {
  width: 12px;
  height: 12px;
  top: 2px;
  left: 2px;
}

/* Normal slider */
.slider-normal {
  width: 20px;
  height: 20px;
  top: 2px;
  left: 2px;
}

/* Large slider */
.slider-large {
  width: 28px;
  height: 28px;
  top: 2px;
  left: 2px;
}

/* Slider positions */
.slider-inactive {
  transform: translateX(0);
}

.slider-active.slider-small {
  transform: translateX(12px);
}

.slider-active.slider-normal {
  transform: translateX(20px);
}

.slider-active.slider-large {
  transform: translateX(24px);
}

/* Dark mode */
.dark .toggle-slider {
  background: #e5e7eb;
}

.dark .toggle-gray {
  background-color: #6b7280;
}
</style>
