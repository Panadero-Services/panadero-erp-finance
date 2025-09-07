<script setup>
import { ref, computed } from 'vue'
import { useInventoryStore } from '../../stores/inventoryStore.js'
import { useScaling } from '../../../../shared/composables/useScaling.js'

const store = useInventoryStore()
const { fontSizes, scalingStyles } = useScaling()

const moduleInfo = ref({
  name: 'ERP Inventory',
  version: '1.0.0',
  description: 'Complete inventory management system with stock tracking, warehousing, and supply chain management',
  features: [
    'Real-time stock tracking',
    'Multi-warehouse management',
    'Purchase order management',
    'Supplier relationship management',
    'Inventory reporting and analytics',
    'AI-powered optimization',
    'Workflow integration',
    'Dynamic font sizing',
    'Action buttons',
    'Agent portal for AI integration'
  ],
  capabilities: [
    'Stock level monitoring',
    'Low stock alerts',
    'Stock movement tracking',
    'Warehouse capacity management',
    'Purchase order creation and approval',
    'Supplier performance tracking',
    'Comprehensive reporting',
    'AI-powered insights and optimization'
  ]
})

const technicalSpecs = ref({
  framework: 'Vue 3',
  stateManagement: 'Pinia',
  styling: 'Tailwind CSS',
  icons: 'Font Awesome',
  scaling: 'Dynamic font sizing',
  workflow: 'Integrated workflow system',
  ai: 'AI agent portal integration'
})
</script>

<template>
  <div class="info-section-2">
    <!-- Header -->
    <div class="mb-8">
      <h2 :style="{ fontSize: `${fontSizes.large}px` }" class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
        Module Information
      </h2>
      <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
        Detailed information about the ERP Inventory module
      </p>
    </div>

    <!-- Module Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Basic Info -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Module Details
        </h3>
        
        <div class="space-y-3">
          <div>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Name:</span>
            <span class="ml-2 text-gray-900 dark:text-white">{{ moduleInfo.name }}</span>
          </div>
          <div>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Version:</span>
            <span class="ml-2 text-gray-900 dark:text-white">{{ moduleInfo.version }}</span>
          </div>
          <div>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Description:</span>
            <p class="mt-1 text-gray-900 dark:text-white">{{ moduleInfo.description }}</p>
          </div>
        </div>
      </div>

      <!-- Technical Specs -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Technical Specifications
        </h3>
        
        <div class="space-y-3">
          <div v-for="(value, key) in technicalSpecs" :key="key">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 capitalize">
              {{ key.replace(/([A-Z])/g, ' $1').trim() }}:
            </span>
            <span class="ml-2 text-gray-900 dark:text-white">{{ value }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Features and Capabilities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Features -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Key Features
        </h3>
        
        <ul class="space-y-2">
          <li
            v-for="feature in moduleInfo.features"
            :key="feature"
            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
          >
            <i class="fas fa-check text-green-500 mr-2"></i>
            {{ feature }}
          </li>
        </ul>
      </div>

      <!-- Capabilities -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 :style="{ fontSize: `${fontSizes.medium}px` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Capabilities
        </h3>
        
        <ul class="space-y-2">
          <li
            v-for="capability in moduleInfo.capabilities"
            :key="capability"
            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
          >
            <i class="fas fa-cog text-blue-500 mr-2"></i>
            {{ capability }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.info-section-2 {
  padding: 0;
}
</style>
