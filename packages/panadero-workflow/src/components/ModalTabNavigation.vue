<!--
  Modal Tab Navigation Component
  @version 1.1.0
  @description Tab navigation for modal left column - modular extraction
-->
<script setup>
import { computed } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  activeTab: {
    type: String,
    default: 'steps'
  },
  leftTabs: {
    type: Array,
    default: () => [
      { id: 'steps', name: 'All Steps', icon: 'fas fa-list-ol' },
      { id: 'info', name: 'Information', icon: 'fas fa-info-circle' },
      { id: 'config', name: 'Config Details', icon: 'fas fa-cog' },
      { id: 'history', name: 'History', icon: 'fas fa-history' }
    ]
  }
})

// Emits
const emit = defineEmits(['tab-change'])

// Composables
const settings = useWorkflowSettings()

// Dynamic font sizes
const _bodySmall = computed(() => `${settings.fontSizesComputed.value.bodySmall}px`)
</script>

<template>
  <div class="px-2 sm:px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
    <nav class="flex flex-wrap gap-2 sm:gap-4">
      <button
        v-for="tab in leftTabs"
        :key="tab.id"
        @click="emit('tab-change', tab.id)"
        :class="[
          'py-2 px-2 sm:px-3 rounded-lg font-medium text-xs sm:text-sm transition-colors flex-shrink-0',
          activeTab === tab.id
            ? 'bg-blue-600 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'
        ]"
        :style="{ fontSize: _bodySmall }"
      >
        <i :class="tab.icon" class="mr-1 sm:mr-2"></i>
        <span class="hidden sm:inline">{{ tab.name }}</span>
        <span class="sm:hidden">{{ tab.name.split(' ')[0] }}</span>
      </button>
    </nav>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
