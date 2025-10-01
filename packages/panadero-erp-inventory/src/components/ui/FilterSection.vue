<!--
  FilterSection Component
  @version 1.0.0
  @date 23-Sep-2025
  @description Reusable filter section component for organizing filter cards
-->
<template>
  <div class="mb-8">
    <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="font-semibold mb-4">
      {{ title }}
    </h3>
    <div :class="gridClass">
      <FilterCard
        v-for="item in items"
        :key="item.value"
        :label="item.label"
        :count="item.count"
        :is-selected="selectedValue === item.value"
        :icon="item.icon"
        @click="handleItemClick(item.value)"
      />
    </div>
  </div>
</template>

<script setup>
import { useCommonSnippets } from '../../composables/useCommonSnippets.js'
import FilterCard from './FilterCard.vue'

// Get common functionality
const { darkModeClasses, scalingStyles } = useCommonSnippets()

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    required: true
  },
  selectedValue: {
    type: [String, Number],
    required: true
  },
  gridClass: {
    type: String,
    default: 'grid grid-cols-6 gap-3'
  }
})

const emit = defineEmits(['select'])

const handleItemClick = (value) => {
  emit('select', value)
}
</script>
