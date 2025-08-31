<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useScaling } from '../../../../shared/composables/useScaling.js'
import { useFrameworkSettings } from '../../../../shared/composables/useFrameworkSettings.js'
import { WorkflowWrapper } from '../../../../panadero-workflow'

const { fontSizes, scalingStyles, spacing } = useScaling()


</script>

<template>
  <div class="workflow-section">
    <!-- Pass reactive finance settings to workflow module -->
    <WorkflowWrapper />
  </div>
</template>

<style scoped>
/* Workflow section specific styles */
.workflow-section {
  /* Container for workflow module interface */
}
</style>