<script setup>
import SharedButton from './SharedButton.vue'

// Props
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    default: 'fas fa-bolt'
  },
  iconColor: {
    type: String,
    default: 'text-yellow-600'
  },
  actions: {
    type: Array,
    required: true,
    validator: (value) => Array.isArray(value) && value.every(action => 
      action && typeof action.label === 'string' && 
      typeof action.handler === 'function' &&
      typeof action.variant === 'string' &&
      typeof action.icon === 'string'
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

// Emits
const emit = defineEmits(['action-click'])

// Handle action clicks
const handleActionClick = (action) => {
  emit('action-click', action)
  if (action.handler) {
    action.handler()
  }
}
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
    
    <!-- Action Buttons -->
    <div class="space-y-3">
      <SharedButton
        v-for="action in actions"
        :key="action.key || action.label"
        @click="handleActionClick(action)"
        :variant="action.variant"
        :size="action.size || 'normal'"
        :disabled="action.disabled || false"
        :icon-left="action.icon"
        :full-width="true"
        class="w-full justify-center"
      >
        {{ action.label }}
      </SharedButton>
    </div>
  </div>
</template>
