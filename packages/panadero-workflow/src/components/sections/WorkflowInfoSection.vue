<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  scaling: {
    type: Object,
    required: true
  }
})

// Store simulation for demo purposes
const store = {
  fontSizes: { base: 14 }
}

// Workflow statistics
const workflowStats = ref({
  total: 24,
  active: 8,
  completed: 12,
  pending: 4
})

// Recent workflows
const recentWorkflows = ref([
  { id: 1, name: 'Vendor Onboarding', status: 'active', progress: 75, module: 'procurement' },
  { id: 2, name: 'Employee Onboarding', status: 'completed', progress: 100, module: 'hr' },
  { id: 3, name: 'Purchase Request', status: 'pending', progress: 25, module: 'procurement' }
])

// Workflow templates
const workflowTemplates = ref([
  { id: 1, name: 'Vendor Onboarding', steps: 5, complexity: 'high', category: 'procurement' },
  { id: 2, name: 'Employee Onboarding', steps: 7, complexity: 'high', category: 'hr' },
  { id: 3, name: 'Purchase Request', steps: 4, complexity: 'medium', category: 'procurement' }
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
  <div class="workflow-info-section">
    <!-- Header -->
    <div class="mb-8">
      <h1 :style="{ fontSize: scaling.font.pageTitle }" class="font-bold text-gray-900 dark:text-white mb-2">
        Workflow Management System
      </h1>
      <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">
        Streamline your business processes with our comprehensive workflow management solution
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-indigo-600 dark:text-indigo-400">
          {{ workflowStats.total }}
        </div>
        <div :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
          Total Workflows
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-blue-600 dark:text-blue-400">
          {{ workflowStats.active }}
        </div>
        <div :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
          Active
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-green-600 dark:text-green-400">
          {{ workflowStats.completed }}
        </div>
        <div :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
          Completed
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-yellow-600 dark:text-yellow-400">
          {{ workflowStats.pending }}
        </div>
        <div :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
          Pending
        </div>
      </div>
    </div>

    <!-- Recent Workflows -->
    <div class="mb-8">
      <h2 :style="{ fontSize: scaling.font.heading }" class="font-bold text-gray-900 dark:text-white mb-6">
        Recent Workflows
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="workflow in recentWorkflows" :key="workflow.id" 
             class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 :style="{ fontSize: scaling.font.title }" class="font-semibold text-gray-900 dark:text-white mb-1">
              {{ workflow.name }}
            </h3>
            <span :class="getStatusColor(workflow.status)" 
                  :style="{ fontSize: scaling.font.caption }"
                  class="px-3 py-1 rounded-full font-medium">
              {{ workflow.status }}
            </span>
          </div>
          
          <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">
            {{ workflow.module }} workflow
          </p>
          
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

    <!-- Workflow Templates -->
    <div>
      <h2 :style="{ fontSize: scaling.font.heading }" class="font-bold text-gray-900 dark:text-white mb-6">
        Available Templates
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="template in workflowTemplates" :key="template.id" 
             class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 :style="{ fontSize: scaling.font.title }" class="font-semibold text-gray-900 dark:text-white mb-1">
              {{ template.name }}
            </h3>
            <span :class="getModuleColor(template.category)" 
                  :style="{ fontSize: scaling.font.caption }"
                  class="px-3 py-1 rounded-full font-medium">
              {{ template.category }}
            </span>
          </div>
          
          <div class="space-y-2">
            <div class="flex justify-between">
              <span :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">Steps:</span>
              <span :style="{ fontSize: scaling.font.body }" class="text-gray-900 dark:text-white">{{ template.steps }}</span>
            </div>
            <div class="flex justify-between">
              <span :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">Complexity:</span>
              <span :style="{ fontSize: scaling.font.body }" class="text-gray-900 dark:text-white capitalize">{{ template.complexity }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.workflow-info-section {
  padding: 1rem;
}
</style>
