<!--
  Active Workflows List Component
  @version 1.1.0
  @description Displays and manages active workflow instances - modular extraction
-->
<script setup>
import { computed } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import ActiveWorkflowCard from './ActiveCard.vue'

// Props
const props = defineProps({
  activeWorkflows: { type: Array, required: true },
  scaling: { type: Object, default: true}
})

// Emits
const emit = defineEmits(['open-modal', 'delete-workflow'])

// Dynamic font sizes
const _base = computed(() => `${props.scaling.base}`)
const _body = computed(() => `${props.scaling.font.body}`)

const _gridLayout = computed(() => { 
  if (_base.value < 10) return `grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-2`
  if (_base.value < 15) return `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4`
  return `grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6`
})

// Methods
function handleOpenModal(workflow) {
  emit('open-modal', workflow)
}

function handleDeleteWorkflow(workflow) {
  emit('delete-workflow', workflow)
}
</script>

<template>
  <div class="mb-8">
    <h2 :style="{ fontSize: _body }" class="text-gray-500 mb-3">
      <i class="fas fa-play-circle mr-2 text-green-600"></i>
      Active Workflows ({{ activeWorkflows.length }}) base:{{_base}}
    </h2>
    <div :class="_gridLayout">
      <ActiveWorkflowCard 
        v-for="activeWorkflow in activeWorkflows" 
        :key="activeWorkflow.workflowNr"
        :active-workflow="activeWorkflow"
        :scaling="scaling"
        @open="handleOpenModal"
        @delete="handleDeleteWorkflow"
      />
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
