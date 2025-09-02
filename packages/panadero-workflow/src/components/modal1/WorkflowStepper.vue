<!--
  Workflow Stepper Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Vertical stepper component for workflow progress display
-->
<script setup>
// Props
const props = defineProps({
  workflowSteps: {
    type: Array,
    required: true
  },
  currentStep: {
    type: Number,
    default: 0
  },
  scaling: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <!-- Vertical Stepper with Progress Bars -->
  <div class="flex-1 overflow-y-auto p-2 mt-0 ">
    <!-- Stepper Header -->
    <div class="mb-4">
      <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mb-2">
        Workflow Steps {{ currentStep + 1 }}/{{ workflowSteps.length }}
      </h3>
    </div>
    
    <nav aria-label="Workflow Progress" class="bg-gray-100 dark:bg-gray-800 p-4 border dark:border-gray-700 rounded-lg">
      <ol role="list" class="overflow-hidden space-y-3">
        <li v-for="(step, stepIdx) in workflowSteps" :key="stepIdx" 
            :class="[stepIdx !== workflowSteps.length - 1 ? 'pb-3' : '', 'relative']">
          
          <!-- Progress Line -->
          <div v-if="stepIdx !== workflowSteps.length - 1" 
               class="absolute left-0 mt-0.5 -ml-px h-full w-0.5"
               :style="{ top: `calc(${scaling.icon.medium} / 2)` }"
               :class="stepIdx < currentStep ? 'bg-indigo-600 dark:bg-indigo-500' : 'bg-gray-300 dark:bg-gray-700'"
               aria-hidden="true" />
          
          <!-- Step Content -->
          <div class="group relative flex items-start">
            <!-- Step Circle -->
            <span class="flex items-center" :style="{ height: scaling.icon.medium }">
              <span class="relative z-10 flex items-center justify-center rounded-full"
                    :style="{ width: scaling.icon.medium, height: scaling.icon.medium }"
                    :class="stepIdx < currentStep ? 'bg-indigo-600 group-hover:bg-indigo-800 dark:bg-indigo-500 dark:group-hover:bg-indigo-600' : 
                           stepIdx === currentStep ? 'border-2 border-indigo-600 bg-white dark:border-indigo-500 dark:bg-gray-900' : 
                           'border-2 border-gray-300 bg-white group-hover:border-gray-400 dark:border-white/15 dark:bg-gray-900 dark:group-hover:border-white/25'">
                
                <!-- Check Icon for completed steps -->
                <i v-if="stepIdx < currentStep" class="fas fa-check text-white" :style="{ fontSize: scaling.font.caption }"></i>
                
                <!-- Current step indicator -->
                <span v-else-if="stepIdx === currentStep" 
                      :style="{ width: scaling.space.small, height: scaling.space.small }"
                      class="rounded-full bg-indigo-600 dark:bg-indigo-500"></span>
                
                <!-- Upcoming step indicator -->
                <span v-else 
                      :style="{ width: scaling.space.small, height: scaling.space.small }"
                      class="rounded-full bg-transparent group-hover:bg-gray-300 dark:group-hover:bg-white/15"></span>
              </span>
            </span>
            
            <!-- Step Info -->
            <span class="ml-3 flex min-w-0 flex-col">
              <span :style="{ fontSize: scaling.font.body }" class="font-medium "
                    :class="stepIdx < currentStep ? 'text-gray-900 dark:text-white' : 
                           stepIdx === currentStep ? 'text-indigo-600 dark:text-indigo-400' : 
                           'text-gray-700 dark:text-gray-300'">
                {{ step.name || `Step ${stepIdx + 1}` }}
              </span>
              <span :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
                {{ step.description || 'Step description' }}
              </span>
            </span>
          </div>
        </li>
      </ol>
    </nav>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>