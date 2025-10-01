<script setup>
// Props
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    default: 'fas fa-chart-bar'
  },
  iconColor: {
    type: String,
    default: 'text-orange-600'
  },
  distribution: {
    type: Array,
    required: true,
    validator: (value) => Array.isArray(value) && value.every(item => 
      item && typeof item.name === 'string' && 
      typeof item.percentage === 'number' && 
      typeof item.count === 'number' &&
      typeof item.color === 'string'
    )
  },
  // Add these props for styling
  darkModeClasses: {
    type: Object,
    required: true
  },
  scalingStyles: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div :class="[darkModeClasses.card, 'rounded-xl shadow-lg border p-6']">
    <div class="flex items-center justify-between mb-6">
      <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="font-semibold">
        {{ title }}
      </h3>
      <div class="rounded-full p-2" :class="darkModeClasses.bgSecondary">
        <i :class="[icon, iconColor]" :style="scalingStyles.iconSizeSmall"></i>
      </div>
    </div>
    
    <!-- GitHub-style progress bar -->
    <div class="mb-4">
      <div class="flex rounded-lg overflow-hidden h-3" :class="darkModeClasses.bgSecondary">
        <div 
          v-for="(item, index) in distribution" 
          :key="item.name"
          :style="{ 
            width: `${item.percentage}%`,
            backgroundColor: item.color
          }"
          :title="`${item.name}: ${item.percentage.toFixed(1)}%`"
          class="h-full transition-all duration-300 hover:opacity-80"
        ></div>
      </div>
    </div>

    <!-- Distribution list with percentages -->
    <div class="space-y-2">
      <div 
        v-for="item in distribution" 
        :key="item.name" 
        class="flex items-center justify-between group hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg p-2 -mx-2 transition-colors"
      >
        <div class="flex items-center gap-3">
          <!-- Color indicator -->
          <div 
            :style="{ backgroundColor: item.color }"
            class="w-3 h-3 rounded-full flex-shrink-0"
          ></div>
          <!-- Item name -->
          <span :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="font-medium">
            {{ item.name }}
          </span>
        </div>
        
        <!-- Percentage and count -->
        <div class="flex items-center gap-3">
          <span :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="font-semibold">
            {{ item.percentage.toFixed(1) }}%
          </span>
          <span :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-right min-w-[3rem]">
            {{ item.count }} items
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
