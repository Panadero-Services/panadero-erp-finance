<script setup>
import { ref, computed } from 'vue'
import { useComplianceStore } from '../../stores/complianceStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const store = useComplianceStore()
const { fontSizes, scalingStyles } = useScaling()
</script>

<template>
  <div class="ui-elements-section">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        UI Elements & Components
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Value Cards
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Display compliance metrics and KPIs with color-coded status indicators
          </p>
        </div>
        
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Status Badges
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Show compliance status, risk levels, and audit states
          </p>
        </div>
        
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Action Buttons
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Interactive buttons for compliance actions and workflows
          </p>
        </div>
        
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Data Tables
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Sortable and filterable tables for compliance data
          </p>
        </div>
        
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Progress Bars
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Visual progress indicators for compliance completion
          </p>
        </div>
        
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-2">
            Charts & Graphs
          </h4>
          <p :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">
            Data visualization for compliance trends and analytics
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ui-elements-section {
  padding: 0;
}
</style>
