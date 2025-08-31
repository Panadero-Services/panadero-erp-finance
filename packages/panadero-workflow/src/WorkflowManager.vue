<!--
  Workflow Manager Component
  @version 1.1.0
  @description Main workflow management interface
-->
<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  workflowStore: {
    type: Object,
    required: true
  }
})

// State
const activeTab = ref('workflows')
const searchQuery = ref('')
const selectedWorkflow = ref(null)

// Computed properties
const filteredWorkflows = computed(() => {
  if (!searchQuery.value) return props.workflowStore.workflowInstances
  
  const query = searchQuery.value.toLowerCase()
  return props.workflowStore.workflowInstances.filter(workflow => 
    workflow.name.toLowerCase().includes(query) ||
    workflow.description.toLowerCase().includes(query)
  )
})

const activeWorkflows = computed(() => 
  filteredWorkflows.value.filter(w => w.status === 'active')
)

const completedWorkflows = computed(() => 
  filteredWorkflows.value.filter(w => w.status === 'completed')
)

const pendingWorkflows = computed(() => 
  filteredWorkflows.value.filter(w => w.status === 'pending')
)

// Methods
const selectWorkflow = (workflow) => {
  selectedWorkflow.value = workflow
}

const clearSelection = () => {
  selectedWorkflow.value = null
}

const getStatusColor = (status) => {
  switch (status) {
    case 'active': return 'text-green-600 bg-green-100'
    case 'completed': return 'text-blue-600 bg-blue-100'
    case 'pending': return 'text-yellow-600 bg-yellow-100'
    case 'failed': return 'text-red-600 bg-red-100'
    default: return 'text-gray-600 bg-gray-100'
  }
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        Workflow Manager
      </h1>
      <p class="mt-2 text-gray-600 dark:text-gray-400">
        Manage and monitor all your workflow instances
      </p>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search workflows..."
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <div class="flex gap-2">
          <button
            v-for="tab in ['workflows', 'templates', 'analytics']"
            :key="tab"
            @click="activeTab = tab"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-colors',
              activeTab === tab
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
            ]"
          >
            {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
          </button>
        </div>
      </div>
        </div>
        
    <!-- Workflows Grid -->
    <div v-if="activeTab === 'workflows'" class="space-y-6">
      <!-- Active Workflows -->
      <div v-if="activeWorkflows.length > 0">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Active Workflows ({{ activeWorkflows.length }})
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="workflow in activeWorkflows"
            :key="workflow.id"
            @click="selectWorkflow(workflow)"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-2">
              <h3 class="font-medium text-gray-900 dark:text-white">
                {{ workflow.name }}
              </h3>
              <span :class="getStatusColor(workflow.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                {{ workflow.status }}
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
              {{ workflow.description }}
            </p>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
              <span>Step {{ (workflow.currentStep || 0) + 1 }} of {{ workflow.steps?.length || 0 }}</span>
              <span>{{ new Date(workflow.created_at).toLocaleDateString() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Workflows -->
      <div v-if="completedWorkflows.length > 0">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Completed Workflows ({{ completedWorkflows.length }})
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="workflow in completedWorkflows"
            :key="workflow.id"
            @click="selectWorkflow(workflow)"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-2">
              <h3 class="font-medium text-gray-900 dark:text-white">
                {{ workflow.name }}
              </h3>
              <span :class="getStatusColor(workflow.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                {{ workflow.status }}
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
              {{ workflow.description }}
            </p>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
              <span>Completed</span>
              <span>{{ new Date(workflow.completed_at || workflow.updated_at).toLocaleDateString() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- No Workflows Message -->
      <div v-if="filteredWorkflows.length === 0" class="text-center py-12">
        <div class="text-gray-400 dark:text-gray-500 mb-4">
          <i class="fas fa-folder-open text-6xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
          No Workflows Found
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
          {{ searchQuery ? 'Try adjusting your search terms.' : 'Create your first workflow to get started.' }}
        </p>
            </div>
            </div>

    <!-- Templates Tab -->
    <div v-else-if="activeTab === 'templates'" class="space-y-6">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        Workflow Templates
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="template in props.workflowStore.builtInTemplates"
          :key="template.id"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"
        >
          <h3 class="font-medium text-gray-900 dark:text-white mb-2">
            {{ template.name }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            {{ template.description }}
          </p>
          <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>{{ template.steps?.length || 0 }} steps</span>
            <span>{{ template.module || 'General' }}</span>
          </div>
        </div>
            </div>
            </div>

    <!-- Analytics Tab -->
    <div v-else-if="activeTab === 'analytics'" class="space-y-6">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        Workflow Analytics
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
            {{ props.workflowStore.workflowInstances.length }}
          </div>
          <div class="text-gray-600 dark:text-gray-400">Total Workflows</div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
            {{ props.workflowStore.activeWorkflows.length }}
            </div>
          <div class="text-gray-600 dark:text-gray-400">Active Workflows</div>
            </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
            {{ props.workflowStore.completedWorkflows.length }}
          </div>
          <div class="text-gray-600 dark:text-gray-400">Completed</div>
        </div>
      </div>
      </div>

    <!-- Selected Workflow Modal -->
    <div
      v-if="selectedWorkflow"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click="clearSelection"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
              {{ selectedWorkflow.name }}
          </h2>
            <button
              @click="clearSelection"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>

          <div class="space-y-4">
            <div>
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Description</h3>
              <p class="text-gray-600 dark:text-gray-400">{{ selectedWorkflow.description }}</p>
            </div>
            
            <div>
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Status</h3>
              <span :class="getStatusColor(selectedWorkflow.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                {{ selectedWorkflow.status }}
              </span>
            </div>
            
            <div v-if="selectedWorkflow.steps && selectedWorkflow.steps.length > 0">
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Steps</h3>
              <div class="space-y-2">
                <div
                  v-for="(step, index) in selectedWorkflow.steps"
                  :key="index"
                  class="flex items-center space-x-3 p-2 bg-gray-50 dark:bg-gray-700 rounded"
                >
                  <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">
                    {{ index + 1 }}
                  </span>
                  <span class="text-gray-900 dark:text-white">{{ step.name }}</span>
                  <span :class="getStatusColor(step.status)" class="ml-auto px-2 py-1 text-xs font-medium rounded-full">
                    {{ step.status }}
                  </span>
          </div>
        </div>
      </div>

            <div>
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Created</h3>
              <p class="text-gray-600 dark:text-gray-400">
                {{ new Date(selectedWorkflow.created_at).toLocaleString() }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
