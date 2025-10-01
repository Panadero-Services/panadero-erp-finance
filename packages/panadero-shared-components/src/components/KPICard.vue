<script setup>
// Props
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    default: 'fas fa-chart-pie'
  },
  iconColor: {
    type: String,
    default: 'text-blue-600'
  },
  kpis: {
    type: Array,
    required: true,
    validator: (value) => Array.isArray(value) && value.every(kpi => 
      kpi && typeof kpi.label === 'string' && kpi.value !== undefined
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
        <i :class="[icon, iconColor]" :style="scalingStyles.iconSize"></i>
      </div>
    </div>
    
    <!-- KPI Content -->
    <div class="space-y-4">
      <div v-for="kpi in kpis" :key="kpi.key || kpi.label" 
           :class="[
             kpi.bgColor || darkModeClasses.bgSecondary, 
             'flex justify-between items-center p-3 rounded-lg border',
             kpi.bgColor ? 'border-transparent' : ''
           ]">
        <div class="flex items-center gap-2">
          <i v-if="kpi.icon" :class="[kpi.icon, kpi.iconColor || kpi.colorClass || darkModeClasses.textSecondary]" :style="scalingStyles.iconSizeSmall"></i>
          <span :style="scalingStyles.textFontSize" :class="darkModeClasses.textSecondary">
            {{ kpi.label }}
          </span>
        </div>
        <span :style="scalingStyles.textFontSize" :class="[kpi.colorClass || darkModeClasses.text]" class="font-bold">
          {{ kpi.value }}
        </span>
      </div>
    </div>
  </div>
</template>
