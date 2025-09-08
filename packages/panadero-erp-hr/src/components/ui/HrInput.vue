<script setup>
import { computed } from 'vue';
import { useScaling } from '../../../../shared/composables/useScaling.js';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
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
  },
  rows: {
    type: Number,
    default: 3
  }
});

const emit = defineEmits(['update:modelValue']);

const { fontSizes } = useScaling();

const inputClasses = computed(() => {
  const baseClasses = 'w-full border rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500';
  
  const stateClasses = props.error 
    ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
    : 'border-gray-300 dark:border-gray-600';
  
  const disabledClasses = props.disabled 
    ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' 
    : 'bg-white dark:bg-gray-700';
  
  const textClasses = 'text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400';
  
  return `${baseClasses} ${stateClasses} ${disabledClasses} ${textClasses}`;
});

const handleInput = (event) => {
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
    
    <input
      v-if="type !== 'textarea'"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :class="inputClasses"
      :style="{ fontSize: `${fontSizes.base}px`, padding: '8px 12px' }"
      @input="handleInput"
    />
    
    <textarea
      v-else
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :rows="rows"
      :class="inputClasses"
      :style="{ fontSize: `${fontSizes.base}px`, padding: '8px 12px' }"
      @input="handleInput"
    ></textarea>
    
    <p 
      v-if="error" 
      :style="{ fontSize: `${fontSizes.xs}` }" 
      class="text-red-600 dark:text-red-400"
    >
      {{ error }}
    </p>
  </div>
</template>
