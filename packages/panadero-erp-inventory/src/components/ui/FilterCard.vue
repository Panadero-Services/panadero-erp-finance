<!--
  FilterCard Component
  @version 1.0.0
  @date 23-Sep-2025
  @description Reusable filter card component for product filters
-->
<template>
  <div 
    @click="handleClick"
    :class="[
      'p-2 rounded-lg border cursor-pointer transition-all duration-200 hover:shadow-md flex items-center gap-2',
      isSelected 
        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' 
        : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300',
      darkModeClasses.bgCard
    ]"
  >
    <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0" 
         :class="isSelected ? 'bg-indigo-100 dark:bg-indigo-800' : 'bg-gray-100 dark:bg-gray-700'">
      <i :class="[icon, 'text-xs', isSelected ? 'text-indigo-600' : 'text-gray-500']"></i>
    </div>
    <div class="flex-1 min-w-0">
      <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.text" class="font-medium truncate">
        {{ label }}
      </div>
      <div :style="scalingStyles.tinyFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
        {{ count }} items
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCommonSnippets } from '../../composables/useCommonSnippets.js'

// Get common functionality
const { darkModeClasses, scalingStyles } = useCommonSnippets()

defineProps({
  label: {
    type: String,
    required: true
  },
  count: {
    type: Number,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  icon: {
    type: String,
    default: 'fas fa-cube'
  }
})

const emit = defineEmits(['click'])

const handleClick = () => {
  emit('click')
}
</script>
