<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    default: ''
  },
  color: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'red', 'yellow', 'purple', 'indigo', 'pink', 'gray'].includes(value)
  },
  trend: {
    type: Number,
    default: 0
  },
  subtitle: {
    type: String,
    default: ''
  }
})

const colorClasses = computed(() => {
  const colors = {
    blue: 'text-blue-600 dark:text-blue-400',
    green: 'text-green-600 dark:text-green-400',
    red: 'text-red-600 dark:text-red-400',
    yellow: 'text-yellow-600 dark:text-yellow-400',
    purple: 'text-purple-600 dark:text-purple-400',
    indigo: 'text-indigo-600 dark:text-indigo-400',
    pink: 'text-pink-600 dark:text-pink-400',
    gray: 'text-gray-600 dark:text-gray-400'
  }
  return colors[props.color] || colors.blue
})

const trendIcon = computed(() => {
  if (props.trend > 0) return 'fas fa-arrow-up text-green-500'
  if (props.trend < 0) return 'fas fa-arrow-down text-red-500'
  return 'fas fa-minus text-gray-500'
})

const trendText = computed(() => {
  if (props.trend > 0) return `+${props.trend}%`
  if (props.trend < 0) return `${props.trend}%`
  return '0%'
})
</script>

<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
          {{ title }}
        </p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ value }}
        </p>
        <p v-if="subtitle" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          {{ subtitle }}
        </p>
      </div>
      
      <div v-if="icon" class="flex-shrink-0">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="`bg-${color}-50 dark:bg-${color}-900/20`">
          <i :class="[icon, colorClasses]" class="text-xl"></i>
        </div>
      </div>
    </div>
    
    <div v-if="trend !== 0" class="mt-4 flex items-center">
      <i :class="trendIcon" class="text-sm mr-1"></i>
      <span class="text-sm font-medium" :class="trend > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
        {{ trendText }}
      </span>
      <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
    </div>
  </div>
</template>
