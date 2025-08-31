<!--
  Workflow Statistics Component
  @version 1.1.1
  @date 26-Aug-2025
  @description Reusable workflow statistics cards with dynamic scaling - receives store as prop
-->
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import statisticsConfig from '../config/statisticsCards.json'

// Props - RECEIVE the store from parent
const props = defineProps({
  workflowStore: {
    type: Object,
    required: true
  }
})

const settings = useWorkflowSettings()

// REMOVE: const workflowStore = useWorkflowStore() - use props.workflowStore instead

// Compute statistics internally - SEPARATION OF CONCERNS
const stats = computed(() => { // KEEP: stats name as in your template
  try {
    const allWorkflows = props.workflowStore.workflows // Use workflows from passed store
    return {
      totalWorkflows: allWorkflows.length,
      activeWorkflows: allWorkflows.filter(w => w.status === props.workflowStore.WORKFLOW_STATES.ACTIVE).length,
      completedWorkflows: allWorkflows.filter(w => w.status === props.workflowStore.WORKFLOW_STATES.COMPLETED).length,
      templatesAvailable: props.workflowStore.builtInTemplates.length,
      averageCompletionTime: '2.3 days', // TODO: Calculate actual average
      pendingApprovals: allWorkflows.filter(w => w.status === props.workflowStore.WORKFLOW_STATES.PENDING).length
    }
  } catch (error) {
    console.error('Error computing workflow stats:', error)
    return {
      totalWorkflows: 0,
      activeWorkflows: 0,
      completedWorkflows: 0,
      templatesAvailable: 0,
      averageCompletionTime: 'N/A',
      pendingApprovals: 0
    }
  }
})

// Dynamic font sizes - FULL dynamic sizing
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _gridLayout = computed(() => { 
      if (settings.fontSizes.value.base < 12) return `grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-2`;
      if (settings.fontSizes.value.base < 16) return `grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2`;
      return `grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4`;
   });

onMounted(() => {
  console.debug("Statistics.vue mounted - using passed store")
  console.debug("Available workflows:", props.workflowStore.workflows.length)
  console.debug("Available templates:", props.workflowStore.builtInTemplates.length)
})
</script>

<template>
  <!-- Statistics Cards -->
  <div :class="_gridLayout">
    <div 
      v-for="card in statisticsConfig.cards" 
      :key="card.key"
      class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 py-8"
      :style="{ fontSize: _caption, padding: _value }" 
    >
      <div class="flex items-center justify-between" >
        <div>
          <div :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400 font-medium">
            {{ card.label }}
          </div>
          <div 
            :style="{ fontSize: _value }" 
            :class="workflowStore.getColorClasses(card.valueColor, 'value')"
            class="font-bold"
          >
            {{ stats[card.key] }}
          </div>
        </div>
        <div 
          :style="{ fontSize: _caption, padding: _padding }" 
          :class="workflowStore.getColorClasses(card.iconColor, 'bg')"
          class="rounded-lg"
        >
          <i :class="[card.icon, workflowStore.getColorClasses(card.iconColor, 'icon')]"></i>
        </div>
      </div>
    </div>
  </div>
</template>