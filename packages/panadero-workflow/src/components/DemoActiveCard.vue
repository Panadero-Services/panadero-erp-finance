<!--
  Demo Active Card Component
  @version 1.1.0
  @description Displays active workflows in a card format with unified scaling system
-->
<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  activeWorkflows: {
    type: Array,
    required: true,
    default: () => []
  },
  // Unified scaling object from useScalingV2
  scaling: {
    type: Object,
    required: true
  }
})

// Computed properties
const hasActiveWorkflows = computed(() => props.activeWorkflows.length > 0)
</script>

<template>
  <!-- Active Workflows Section -->
  <div 
    v-if="hasActiveWorkflows" 
    :style="{ 
      marginBottom: scaling.space.large,
      padding: scaling.space.medium
    }" 
    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
  >
    <!-- Section Header -->
    <div 
      :style="{ marginBottom: scaling.space.small }"
      class="flex items-center gap-3"
    >
      <div 
        :style="{ width: scaling.icon.medium, height: scaling.icon.medium }"
        class="bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center"
      >
        <i class="fas fa-play-circle text-green-600 dark:text-green-400"></i>
      </div>
      <h2 
        :style="{ fontSize: scaling.font.heading }" 
        class="font-bold text-gray-900 dark:text-white"
      >
        Active Workflows
      </h2>
    </div>
    
    <!-- Workflow Cards Grid -->
    <div 
      :style="{ gap: scaling.space.small }"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
    >
      <!-- Individual Workflow Card -->
      <div
        v-for="workflow in activeWorkflows"
        :key="workflow.id"
        :style="{ 
          padding: scaling.space.small,
          borderRadius: scaling.card.borderRadius
        }"
        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg hover:shadow-lg hover:scale-105 hover:border-green-300 dark:hover:border-green-500 transition-all duration-300 ease-out cursor-pointer"
      >
        <!-- Card Header -->
        <div 
          :style="{ marginBottom: scaling.space.small }"
          class="flex items-center justify-between"
        >
          <h3 
            :style="{ fontSize: scaling.font.title }"
            class="font-semibold text-gray-900 dark:text-white truncate"
          >
            {{ workflow.name }}
          </h3>
          <span 
            :style="{
              padding: scaling.button.padding,
              fontSize: scaling.font.caption
            }"
            class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full font-medium"
          >
            {{ workflow.status }}
          </span>
        </div>
        
        <!-- Workflow Description -->
        <p 
          :style="{ 
            fontSize: scaling.font.body,
            marginBottom: scaling.space.small,
            lineHeight: '1.5'
          }" 
          class="text-gray-600 dark:text-gray-400"
        >
          {{ workflow.description }}
        </p>
        
        <!-- Progress Information -->
        <div 
          :style="{ gap: scaling.space.tiny }"
          class="space-y-3"
        >
          <!-- Progress Text -->
          <div 
            :style="{ fontSize: scaling.font.sm }"
            class="flex justify-between text-sm"
          >
            <span class="text-gray-500 dark:text-gray-400">Progress:</span>
            <span class="font-medium text-gray-900 dark:text-white">
              Step {{ (workflow.currentStep || 0) + 1 }} of {{ workflow.steps?.length || 0 }}
            </span>
          </div>
          
          <!-- Progress Bar -->
          <div 
            :style="{ 
              height: scaling.space.tiny,
              borderRadius: scaling.button.borderRadius
            }"
            class="w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner"
          >
            <div 
              :style="{ 
                width: `${workflow.steps ? ((workflow.currentStep + 1) / workflow.steps.length) * 100 : 0}%`,
                height: scaling.space.tiny,
                borderRadius: scaling.button.borderRadius
              }"
              class="bg-gradient-to-r from-green-500 to-green-600 rounded-full transition-all duration-500 ease-out shadow-sm"
            ></div>
          </div>
          
          <!-- Creation Date -->
          <div 
            :style="{ 
              fontSize: scaling.font.sm,
              marginTop: scaling.space.tiny
            }"
            class="flex justify-between text-sm text-gray-500 dark:text-gray-400"
          >
            <span>Created:</span>
            <span>{{ new Date(workflow.created_at).toLocaleDateString() }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom hover effects */
.hover\:shadow-lg:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced hover effects */
.hover\:scale-105:hover {
  transform: scale(1.05);
}

/* Progress bar glow effect */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

/* Card entrance animation */
@keyframes cardEntrance {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Apply animation to cards */
.grid > div {
  animation: cardEntrance 0.6s ease-out forwards;
}

.grid > div:nth-child(1) { animation-delay: 0.1s; }
.grid > div:nth-child(2) { animation-delay: 0.2s; }
.grid > div:nth-child(3) { animation-delay: 0.3s; }

/* Dark mode enhancements */
.dark .bg-green-900\/20 {
  background-color: rgba(6, 78, 59, 0.2);
}

.dark .border-green-700 {
  border-color: rgb(4, 120, 87);
}

/* Enhanced shadows for dark mode */
.dark .shadow-inner {
  box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.3);
}

.dark .shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.4);
}
</style>
