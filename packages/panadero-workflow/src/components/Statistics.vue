<!--
  Workflow Statistics Component
  @version 1.0.10
  @date 22-Aug-2025
  @description Reusable workflow statistics cards with dynamic scaling
-->
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import { useWorkflowStore } from '../composables/workflowStore.js'
import statisticsConfig from '../config/statisticsCards.json'

const settings = useWorkflowSettings()
const workflowStore = useWorkflowStore()

// Props
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalWorkflows: 12,
      activeWorkflows: 5,
      completedWorkflows: 7,
      templatesAvailable: 8,
      averageCompletionTime: '2.3 days',
      pendingApprovals: 4
    })
  }
})

onMounted(() => {
  console.debug("settings.fontSizesComputed.value.caption:");
  console.debug(settings.fontSizesComputed.value.caption);
  console.debug("settings.fontSizesComputed.value.h2:");
  console.debug(settings.fontSizesComputed.value.h2);
})

// Dynamic font sizes
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _gridLayout = computed(() => { 
      if (settings.fontSizes.value.base < 12) return `grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-2`;
      if (settings.fontSizes.value.base < 16) return `grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2`;
      return `grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4`;
 });

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








