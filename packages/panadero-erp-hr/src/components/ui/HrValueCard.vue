<script setup>
import { computed } from 'vue';
import { useScaling } from '../../../../shared/composables/useScaling.js';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  color: {
    type: String,
    default: 'green',
    validator: (value) => ['green', 'blue', 'purple', 'orange', 'red', 'yellow', 'indigo', 'pink'].includes(value)
  },
  trend: {
    type: Object,
    default: null
  }
});

const { fontSizes } = useScaling();

const colorClasses = computed(() => {
  const colors = {
    green: 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
    blue: 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
    purple: 'bg-purple-50 border-purple-200 dark:bg-purple-900/20 dark:border-purple-800',
    orange: 'bg-orange-50 border-orange-200 dark:bg-orange-900/20 dark:border-orange-800',
    red: 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
    yellow: 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800',
    indigo: 'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-800',
    pink: 'bg-pink-50 border-pink-200 dark:bg-pink-900/20 dark:border-pink-800'
  };
  return colors[props.color] || colors.green;
});

const iconColorClasses = computed(() => {
  const colors = {
    green: 'text-green-600 dark:text-green-400',
    blue: 'text-blue-600 dark:text-blue-400',
    purple: 'text-purple-600 dark:text-purple-400',
    orange: 'text-orange-600 dark:text-orange-400',
    red: 'text-red-600 dark:text-red-400',
    yellow: 'text-yellow-600 dark:text-yellow-400',
    indigo: 'text-indigo-600 dark:text-indigo-400',
    pink: 'text-pink-600 dark:text-pink-400'
  };
  return colors[props.color] || colors.green;
});

const trendClasses = computed(() => {
  if (!props.trend) return '';
  
  const baseClasses = 'inline-flex items-center text-sm font-medium';
  
  if (props.trend.direction === 'up') {
    return `${baseClasses} text-green-600 dark:text-green-400`;
  } else if (props.trend.direction === 'down') {
    return `${baseClasses} text-red-600 dark:text-red-400`;
  } else {
    return `${baseClasses} text-gray-600 dark:text-gray-400`;
  }
});
</script>

<template>
  <div :class="['rounded-lg border p-6', colorClasses]">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400 font-medium">
          {{ title }}
        </p>
        <p :style="{ fontSize: `${fontSizes.h1}` }" class="text-gray-900 dark:text-white font-bold mt-1">
          {{ value }}
        </p>
        <p v-if="subtitle" :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400 mt-1">
          {{ subtitle }}
        </p>
        <div v-if="trend" :class="trendClasses" class="mt-2">
          <i :class="trend.direction === 'up' ? 'fas fa-arrow-up' : trend.direction === 'down' ? 'fas fa-arrow-down' : 'fas fa-minus'" class="mr-1"></i>
          {{ trend.value }}
          <span v-if="trend.period" class="ml-1">{{ trend.period }}</span>
        </div>
      </div>
      <div v-if="icon" :class="['flex-shrink-0', iconColorClasses]">
        <i :class="[icon, 'text-2xl']"></i>
      </div>
    </div>
  </div>
</template>
