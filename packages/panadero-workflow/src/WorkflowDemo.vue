<!--
  Workflow Demo Component
  @version 1.1.0
  @description Demo component for showcasing workflow functionality
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
const selectedTemplate = ref(null)
const showCreateForm = ref(false)

// Computed properties
const availableTemplates = computed(() => props.workflowStore.builtInTemplates)
const activeWorkflows = computed(() => props.workflowStore.activeWorkflows)
const completedWorkflows = computed(() => props.workflowStore.completedWorkflows)

// Methods
const selectTemplate = (template) => {
  selectedTemplate.value = template
  showCreateForm.value = true
}

const createWorkflow = async () => {
  if (!selectedTemplate.value) return
  
  try {
    const instance = props.workflowStore.createWorkflowInstance(selectedTemplate.value.id, {
      created_by: 'demo_user'
    })
    
    // Start the workflow
    await props.workflowStore.startWorkflow(instance.id)
    
    // Reset form
    showCreateForm.value = false
    selectedTemplate.value = null
    
    console.log('Workflow created and started:', instance)
  } catch (error) {
    console.error('Failed to create workflow:', error)
  }
}

const closeForm = () => {
  showCreateForm.value = false
  selectedTemplate.value = null
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
        Workflow Management Demo
      </h1>
      <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
        Experience the power of our workflow management system with this interactive demo.
        Create workflows, monitor progress, and see how everything works together.
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
          {{ availableTemplates.length }}
        </div>
        <div class="text-gray-600 dark:text-gray-400">Available Templates</div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
          {{ activeWorkflows.length }}
        </div>
        <div class="text-gray-600 dark:text-gray-400">Active Workflows</div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
          {{ completedWorkflows.length }}
        </div>
        <div class="text-gray-600 dark:text-gray-400">Completed</div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2">
          {{ props.workflowStore.workflowInstances.length }}
        </div>
        <div class="text-gray-600 dark:text-gray-400">Total Instances</div>
      </div>
    </div>

    <!-- Template Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 mb-8">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
        Choose a Workflow Template
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="template in availableTemplates"
          :key="template.id"
          @click="selectTemplate(template)"
          class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent hover:border-blue-300 dark:hover:border-blue-600"
        >
          <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-project-diagram text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
              {{ template.name }}
            </h3>
            
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              {{ template.description }}
            </p>
            
            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
              <span>{{ template.steps?.length || 0 }} steps</span>
              <span>{{ template.module || 'General' }}</span>
            </div>
            
            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              Create Workflow
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Workflows -->
    <div v-if="activeWorkflows.length > 0" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 mb-8">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        Active Workflows
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="workflow in activeWorkflows"
          :key="workflow.id"
          class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ workflow.name }}
            </h3>
            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full text-sm font-medium">
              {{ workflow.status }}
            </span>
          </div>
          
          <p class="text-gray-600 dark:text-gray-400 mb-4">
            {{ workflow.description }}
          </p>
          
          <div class="space-y-3">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-gray-400">Progress:</span>
              <span class="font-medium text-gray-900 dark:text-white">
                Step {{ (workflow.currentStep || 0) + 1 }} of {{ workflow.steps?.length || 0 }}
              </span>
            </div>
            
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div 
                class="bg-green-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${workflow.steps ? ((workflow.currentStep + 1) / workflow.steps.length) * 100 : 0}%` }"
              ></div>
            </div>
            
            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
              <span>Created: {{ new Date(workflow.created_at).toLocaleDateString() }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Workflow Modal -->
    <div
      v-if="showCreateForm && selectedTemplate"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click="closeForm"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6"
        @click.stop
      >
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Create New Workflow
          </h3>
          <button
            @click="closeForm"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Template
            </label>
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <h4 class="font-medium text-gray-900 dark:text-white">{{ selectedTemplate.name }}</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ selectedTemplate.description }}</p>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Steps
            </label>
            <p class="text-gray-600 dark:text-gray-400">{{ selectedTemplate.steps?.length || 0 }} steps</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Module
            </label>
            <p class="text-gray-600 dark:text-gray-400">{{ selectedTemplate.module || 'General' }}</p>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="closeForm"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="createWorkflow"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Create Workflow
          </button>
        </div>
      </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">
        <i class="fas fa-info-circle mr-2"></i>
        How to Use This Demo
      </h3>
      <div class="text-blue-800 dark:text-blue-200 space-y-2">
        <p>1. <strong>Choose a Template:</strong> Click on any workflow template above to get started</p>
        <p>2. <strong>Create Workflow:</strong> Fill out the form and create your first workflow instance</p>
        <p>3. <strong>Monitor Progress:</strong> Watch as your workflow progresses through its steps</p>
        <p>4. <strong>Complete Steps:</strong> Interact with the workflow to complete individual steps</p>
        <p>5. <strong>Track Results:</strong> See the final outcome and workflow completion</p>
      </div>
    </div>
  </div>
</template>
