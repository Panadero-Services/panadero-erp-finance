<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  scaling: {
    type: Object,
    required: true
  }
})

// Workflow data
const workflows = ref([
  { id: 1, name: 'Vendor Onboarding', status: 'active', progress: 75, module: 'procurement' },
  { id: 2, name: 'Employee Onboarding', status: 'completed', progress: 100, module: 'hr' },
  { id: 3, name: 'Purchase Request', status: 'pending', progress: 25, module: 'procurement' }
])

// Get status color
const getStatusColor = (status) => {
  switch (status) {
    case 'active': return 'text-blue-600 bg-blue-100 dark:bg-blue-900/30'
    case 'completed': return 'text-green-600 bg-green-100 dark:bg-green-900/30'
    case 'pending': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30'
    default: return 'text-gray-600 bg-gray-100 dark:bg-gray-700'
  }
}

// Get module color
const getModuleColor = (module) => {
  switch (module) {
    case 'procurement': return 'text-purple-600 bg-purple-100 dark:bg-purple-900/30'
    case 'hr': return 'text-indigo-600 bg-indigo-100 dark:bg-indigo-900/30'
    case 'finance': return 'text-green-600 bg-green-100 dark:bg-green-900/30'
    default: return 'text-gray-600 bg-gray-100 dark:bg-gray-700'
  }
}
</script>

<template>
  <div class="workflow-section">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="{ fontSize: scaling.font.pageTitle }" class="font-bold text-gray-900 dark:text-white mb-2">
        Workflow Overview
      </h1>
      <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">
        Monitor and manage your active workflows
      </p>
    </div>

    <!-- Workflow List -->
    <div class="space-y-6">
      <div v-for="workflow in workflows" :key="workflow.id" 
           class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 :style="{ fontSize: scaling.font.title }" class="font-semibold text-gray-900 dark:text-white mb-1">
              {{ workflow.name }}
            </h3>
            <span :class="getModuleColor(workflow.module)" 
                  :style="{ fontSize: scaling.font.caption }"
                  class="px-3 py-1 rounded-full font-medium">
              {{ workflow.module }}
            </span>
          </div>
          <span :class="getStatusColor(workflow.status)" 
                :style="{ fontSize: scaling.font.caption }"
                class="px-3 py-1 rounded-full font-medium">
            {{ workflow.status }}
          </span>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-4">
          <div class="flex justify-between text-sm mb-2">
            <span :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">Progress</span>
            <span :style="{ fontSize: scaling.font.caption }" class="text-gray-900 dark:text-white">{{ workflow.progress }}%</span>
          </div>
          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                 :style="{ width: `${workflow.progress}%` }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.workflow-section {
  padding: 1rem;
}
</style>