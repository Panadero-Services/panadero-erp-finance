<!--
  Modal Config Content Component
  @version 1.1.0
  @description Config tab content for modal - modular extraction
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  activeWorkflow: {
    type: Object,
    default: null
  },
  scaling: {
    type: Object,
    required: true
  }
})

// Dynamic font sizes - ONLY changing styling reference
const _value = computed(() => props.scaling.font.title)
</script>

<template>
  <div class="h-full overflow-y-auto p-2 sm:p-4">
    <h3 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white mb-4">
      <i class="fas fa-cog mr-2 text-indigo-600"></i>
      Configuration Details
    </h3>
    <div class="text-center py-8">
      <i class="fas fa-cog text-gray-400 text-3xl mb-4"></i>
      <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
        Configuration details will be loaded dynamically
      </p>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
