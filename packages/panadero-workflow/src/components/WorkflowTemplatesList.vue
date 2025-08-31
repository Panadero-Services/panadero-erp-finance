<!--
  Workflow Templates List Component
  @version 1.1.0
  @description Displays available workflow templates with start actions - modular extraction
-->
<script setup>
import { computed } from 'vue'
import WorkflowCard from './Card.vue'

// Props
const props = defineProps({
  allWorkflows: { type: Array, required: true },
  fontSizes: { type: Object, default: true}
})

// Emits
const emit = defineEmits(['start-workflow'])


// Dynamic font sizes
const _base = computed(() => `${props.fontSizes.base}`)
const _body = computed(() => `${props.fontSizes.body}`)

const _gridLayout = computed(() => { 
  if (_base.value < 10) return `grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-2`
  if (_base.value < 15) return `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4`
  return `grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6`
})

// Methods
function handleStartWorkflow(workflow) {
  emit('start-workflow', workflow)
}
</script>

<template>
  <div>
    <h2 :style="{ fontSize: _body }" class="text-gray-500 mt-8 mb-3">
      <i class="fas fa-play-circle mr-2 text-blue-600"></i>
      Available Templates ({{ allWorkflows.length }}){{_base}}
    </h2>
    <div :class="_gridLayout">
      <WorkflowCard 
        v-for="workflow in allWorkflows" 
        :key="workflow.id"
        :workflow="workflow"
        :fontSizes="fontSizes"
        :clickable="false"
      >
        <!-- Custom actions slot -->
        <template #actions>
          <div class="flex space-x-2 justify-end">
            <button 
              @click="handleStartWorkflow(workflow)"
              class="px-4 py-2 text-blue-700 dark:text-blue-300 border-2 border-blue-500 dark:border-blue-600 rounded-lg hover:bg-blue-500 dark:hover:bg-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              :style="{ fontSize: _body }">
              <i class="fas fa-play mr-2"></i>
              Start
            </button>
          </div>
        </template>
      </WorkflowCard>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
