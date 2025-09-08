<script setup>
import { ref, computed } from 'vue'
import { useComplianceStore } from '../../stores/complianceStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const store = useComplianceStore()
const { fontSizes, scalingStyles } = useScaling()
</script>

<template>
  <div class="info-section-2">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        Compliance Module Overview
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-3">
            Module Capabilities
          </h4>
          <ul class="space-y-2">
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">Regulatory compliance tracking</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">Audit trail management</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">Risk assessment tools</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">AI-powered analysis</span>
            </li>
          </ul>
        </div>
        
        <div>
          <h4 :style="{ fontSize: `${fontSizes.base}px` }" class="font-medium text-gray-900 dark:text-white mb-3">
            Integration Features
          </h4>
          <ul class="space-y-2">
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">Workflow automation</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">Real-time monitoring</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">Custom reporting</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-teal-500 mr-2"></i>
              <span :style="{ fontSize: `${fontSizes.small}px` }" class="text-gray-600 dark:text-gray-400">API integration</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.info-section-2 {
  padding: 0;
}
</style>
