<script setup>
import { computed } from 'vue';
import { useScaling } from '../../../../shared/composables/useScaling.js';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue']);

const { fontSizes } = useScaling();

const selectClasses = computed(() => {
  const baseClasses = 'w-full border rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500';
  
  const stateClasses = props.error 
    ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
    : 'border-gray-300 dark:border-gray-600';
  
  const disabledClasses = props.disabled 
    ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' 
    : 'bg-white dark:bg-gray-700';
  
  const textClasses = 'text-gray-900 dark:text-white';
  
  return `${baseClasses} ${stateClasses} ${disabledClasses} ${textClasses}`;
});

const handleChange = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>

<template>
  <div class="space-y-2">
    <label 
      v-if="label" 
      :style="{ fontSize: `${fontSizes.label}` }" 
      class="block font-medium text-gray-700 dark:text-gray-300"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <select
      :value="modelValue"
      :required="required"
      :disabled="disabled"
      :class="selectClasses"
      :style="{ fontSize: `${fontSizes.base}px`, padding: '8px 12px' }"
      @change="handleChange"
    >
      <option value="" disabled>{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
    
    <p 
      v-if="error" 
      :style="{ fontSize: `${fontSizes.xs}` }" 
      class="text-red-600 dark:text-red-400"
    >
      {{ error }}
    </p>
  </div>
</template>
