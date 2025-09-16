<script setup>
import { computed } from 'vue'
import { useScaling } from 'panadero-shared-styling'

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
  icon: {
    type: String,
    default: ''
  },
  rows: {
    type: Number,
    default: 3
  }
})

const emit = defineEmits(['update:modelValue'])
const { scalingStyles } = useScaling()

const inputClasses = computed(() => {
  const baseClasses = 'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white'
  
  const typeClasses = {
    text: 'px-3 py-2',
    email: 'px-3 py-2',
    password: 'px-3 py-2',
    number: 'px-3 py-2',
    date: 'px-3 py-2',
    textarea: 'px-3 py-2'
  }
  
  const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed' : ''
  
  return `${baseClasses} ${typeClasses[props.type] || typeClasses.text} ${disabledClasses}`
})

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<template>
  <div class="space-y-1">
    <label v-if="label" :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
      <span v-if="required" :style="scalingStyles.smallFontSize" class="text-red-500 ml-1">*</span>
    </label>
    
    <div class="relative">
      <i v-if="icon" :class="icon" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
      
      <input
        v-if="type !== 'textarea'"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="[inputClasses, icon ? 'pl-10' : '']"
        :style="scalingStyles.textFontSize"
        @input="handleInput"
      />
      
      <textarea
        v-else
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        :class="[inputClasses, icon ? 'pl-10' : '']"
        :style="scalingStyles.textFontSize"
        @input="handleInput"
      ></textarea>
    </div>
  </div>
</template>
