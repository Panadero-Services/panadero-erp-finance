<!--
  Step Info Panel Component
  @version 1.0.0
  @description Displays step information using existing step type components in info-only mode
-->
<script setup>
import { computed } from 'vue'
import WorkflowApproval from './WorkflowApproval.vue'
import WorkflowChecklist from './WorkflowChecklist.vue'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowEntitySelection from './WorkflowEntitySelection.vue'

// Props
const props = defineProps({
  step: {
    type: Object,
    required: true
  },
  scaling: {
    type: Object,
    required: true
  }
})

// Get the appropriate component based on step type
const getInfoComponent = computed(() => {
  switch (props.step?.type) {
    case 'approval':
      return WorkflowApproval
    case 'checklist':
      return WorkflowChecklist
    case 'form_submission':
      return WorkflowForm
    case 'shared_entity_selection':
      return WorkflowEntitySelection
    default:
      return null
  }
})

// Check if we have a component for this step type
const hasInfoComponent = computed(() => {
  return getInfoComponent.value !== null
})
</script>

<template>
  <div v-if="hasInfoComponent" class="bg-white dark:bg-gray-800 rounded-lg p-3">
    <component 
      :is="getInfoComponent" 
      :step="step" 
      :step-data="{}"
      :scaling="scaling"
      :info-only="true" />
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
