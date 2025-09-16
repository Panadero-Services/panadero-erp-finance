<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useScaling } from 'panadero-shared-styling'

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
  }
})

const emit = defineEmits(['update:modelValue'])
const { scalingStyles } = useScaling()

const isOpen = ref(false)
const dropdownRef = ref(null)

const selectedOption = computed(() => {
  return props.options.find(option => option.value === props.modelValue) || null
})

const dropdownClasses = computed(() => {
  const baseClasses = 'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white'
  const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
  
  return `${baseClasses} ${disabledClasses}`
})

const handleSelect = (option) => {
  emit('update:modelValue', option.value)
  isOpen.value = false
}

const toggleDropdown = () => {
  if (!props.disabled) {
    isOpen.value = !isOpen.value
  }
}

const closeDropdown = () => {
  isOpen.value = false
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="space-y-1">
    <label v-if="label" :style="scalingStyles.smallFontSize" class="block font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <div ref="dropdownRef" class="relative">
      <button
        type="button"
        :class="dropdownClasses"
        :disabled="disabled"
        @click="toggleDropdown"
      >
        <div class="flex items-center justify-between px-3 py-2">
          <span :style="scalingStyles.textFontSize" class="block truncate">
            {{ selectedOption ? selectedOption.label : placeholder }}
          </span>
          <i :style="scalingStyles.iconSizeSmall" class="fas fa-chevron-down text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': isOpen }"></i>
        </div>
      </button>
      
      <div
        v-if="isOpen"
        :style="scalingStyles.textFontSize"
        class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1  ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none"
      >
        <div
          v-for="option in options"
          :key="option.value"
          class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-700"
          :class="{ 'bg-blue-50 dark:bg-blue-900': option.value === modelValue }"
          @click="handleSelect(option)"
        >
          <span :style="scalingStyles.textFontSize" class="block truncate font-normal" :class="{ 'text-blue-600 dark:text-blue-400': option.value === modelValue }">
            {{ option.label }}
          </span>
          <span v-if="option.value === modelValue" class="absolute inset-y-0 right-0 flex items-center pr-4">
            <i :style="scalingStyles.iconSizeSmall" class="fas fa-check text-blue-600 dark:text-blue-400"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
