<script setup>
import { computed } from 'vue';
import { useFinanceStore } from '../../stores/financeStore';
import { useScaling } from '../../../../shared/composables/useScaling.js';

const store = useFinanceStore();
const { fontSizes, scalingStyles, spacing } = useScaling();

const props = defineProps({
  // Content
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
  
  // Layout
  rows: {
    type: String,
    default: '2-row',
    validator: (value) => ['2-row', '3-row', 'custom'].includes(value)
  },
  alignment: {
    type: String,
    default: 'center',
    validator: (value) => ['left', 'center', 'right'].includes(value)
  },
  
  // Colors
  color: {
    type: String,
    default: 'auto',
    validator: (value) => ['auto', 'positive', 'negative', 'warning', 'info', 'neutral'].includes(value)
  },
  
  // Auto-coloring thresholds (when color='auto')
  minGood: {
    type: Number,
    default: null
  },
  maxGood: {
    type: Number,
    default: null
  },
  minWarning: {
    type: Number,
    default: null
  },
  maxWarning: {
    type: Number,
    default: null
  },
  
  // Styling
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['small', 'normal', 'large'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'bordered', 'elevated'].includes(value)
  },
  
  // Formatting
  format: {
    type: String,
    default: 'text',
    validator: (value) => ['currency', 'number', 'percentage', 'text'].includes(value)
  },
  currency: {
    type: String,
    default: 'USD'
  },
  
  // Optional icon
  icon: {
    type: String,
    default: ''
  },
  
  // Trend indicator
  trend: {
    type: String,
    default: '',
    validator: (value) => ['', 'up', 'down', 'neutral'].includes(value)
  }
});

// Computed styles based on size - FIXED to use .value properly
const sizeStyles = computed(() => {
  const baseSize = fontSizes.value?.base || 14;
  
  const sizes = {
    small: {
      titleSize: `${baseSize - 3}px`,
      valueSize: `${baseSize + 2}px`,
      subtitleSize: `${baseSize - 4}px`,
      padding: '0.75rem'
    },
    normal: {
      titleSize: `${baseSize - 2}px`,
      valueSize: `${baseSize + 6}px`,
      subtitleSize: `${baseSize - 2}px`,
      padding: '1rem'
    },
    large: {
      titleSize: `${baseSize}px`,
      valueSize: `${baseSize + 10}px`,
      subtitleSize: `${baseSize - 1}px`,
      padding: '1.5rem'
    }
  };
  return sizes[props.size];
});

// Auto-color determination based on thresholds
const autoColor = computed(() => {
  if (props.color !== 'auto') return props.color;
  
  const numValue = typeof props.value === 'number' ? props.value : parseFloat(props.value);
  if (isNaN(numValue)) return 'neutral';
  
  // Check good ranges
  if (props.minGood !== null && numValue >= props.minGood) return 'positive';
  if (props.maxGood !== null && numValue <= props.maxGood) return 'positive';
  
  // Check warning ranges
  if (props.minWarning !== null && numValue >= props.minWarning) return 'warning';
  if (props.maxWarning !== null && numValue <= props.maxWarning) return 'warning';
  
  // Default to negative if outside all good/warning ranges and thresholds are set
  if (props.minGood !== null || props.maxGood !== null || props.minWarning !== null || props.maxWarning !== null) {
    return 'negative';
  }
  
  return 'neutral';
});

// Color classes
const colorClasses = computed(() => {
  const colors = {
    positive: 'text-green-600 dark:text-green-400',
    negative: 'text-red-600 dark:text-red-400',
    warning: 'text-yellow-600 dark:text-yellow-400',
    info: 'text-blue-600 dark:text-blue-400',
    neutral: 'text-gray-900 dark:text-white'
  };
  return colors[autoColor.value] || colors.neutral;
});

// Variant classes
const variantClasses = computed(() => {
  const variants = {
    default: 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700',
    bordered: 'bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600',
    elevated: 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg'
  };
  return variants[props.variant];
});

// Alignment classes
const alignmentClasses = computed(() => {
  const alignments = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right'
  };
  return alignments[props.alignment];
});

// Format value
const formattedValue = computed(() => {
  const value = props.value;
  
  switch (props.format) {
    case 'currency':
      const numValue = typeof value === 'number' ? value : parseFloat(value);
      if (isNaN(numValue)) return value;
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: props.currency
      }).format(numValue);
      
    case 'number':
      const num = typeof value === 'number' ? value : parseFloat(value);
      if (isNaN(num)) return value;
      return new Intl.NumberFormat('en-US').format(num);
      
    case 'percentage':
      const percent = typeof value === 'number' ? value : parseFloat(value);
      if (isNaN(percent)) return value;
      return new Intl.NumberFormat('en-US', {
        style: 'percent',
        minimumFractionDigits: 1
      }).format(percent / 100);
      
    default:
      return value;
  }
});

// Trend icon
const trendIcon = computed(() => {
  const icons = {
    up: 'fas fa-arrow-up',
    down: 'fas fa-arrow-down',
    neutral: 'fas fa-minus'
  };
  return icons[props.trend] || '';
});

// Trend color
const trendColor = computed(() => {
  const colors = {
    up: 'text-green-500',
    down: 'text-red-500',
    neutral: 'text-gray-500'
  };
  return colors[props.trend] || '';
});
</script>

<template>
  <div 
    :class="[
      'finance-value-card rounded-lg transition-all duration-200 hover:shadow-md',
      variantClasses,
      alignmentClasses
    ]"
    :style="{ padding: sizeStyles.padding }"
  >
    <!-- 2-row layout -->
    <template v-if="rows === '2-row'">
      <div class="flex items-center justify-between mb-2">
        <h3 
          :style="{ fontSize: sizeStyles.titleSize }"
          class="font-medium text-gray-700 dark:text-gray-300"
        >
          {{ title }}
        </h3>
        <i v-if="icon" :class="icon" class="text-gray-400 dark:text-gray-500"></i>
      </div>
      
      <div class="flex items-center justify-between">
        <div 
          :style="{ fontSize: sizeStyles.valueSize }"
          :class="['font-semibold', colorClasses]"
        >
          {{ formattedValue }}
        </div>
        <i 
          v-if="trend" 
          :class="[trendIcon, trendColor]" 
          :style="{ fontSize: sizeStyles.subtitleSize }"
        ></i>
      </div>
    </template>

    <!-- 3-row layout -->
    <template v-if="rows === '3-row'">
      <div class="flex items-center justify-between mb-2">
        <div 
          :style="{ fontSize: sizeStyles.titleSize }"
          class="text-gray-500 dark:text-gray-400"
        >
          {{ title }}
        </div>
        <i v-if="icon" :class="icon" class="text-gray-400 dark:text-gray-500"></i>
      </div>
      
      <div class="flex items-center justify-between mb-2">
        <div 
          :style="{ fontSize: sizeStyles.valueSize }"
          :class="['font-semibold', colorClasses]"
        >
          {{ formattedValue }}
        </div>
        <i 
          v-if="trend" 
          :class="[trendIcon, trendColor]" 
          :style="{ fontSize: sizeStyles.titleSize }"
        ></i>
      </div>
      
      <div 
        v-if="subtitle"
        :style="{ fontSize: sizeStyles.subtitleSize }"
        class="text-gray-500 dark:text-gray-400"
      >
        {{ subtitle }}
      </div>
    </template>

    <!-- Custom layout (slot-based) -->
    <template v-if="rows === 'custom'">
      <slot 
        :title="title" 
        :value="formattedValue" 
        :subtitle="subtitle"
        :color-classes="colorClasses"
        :size-styles="sizeStyles"
        :trend-icon="trendIcon"
        :trend-color="trendColor"
      />
    </template>
  </div>
</template>

<style scoped>
.finance-value-card {
  min-height: fit-content;
}

/* Hover effect */
.finance-value-card:hover {
  transform: translateY(-1px);
}

/* Dark mode adjustments */
.dark .finance-value-card {
  background-color: #1f2937;
}
</style>