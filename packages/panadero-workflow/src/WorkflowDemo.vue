<!--
  Workflow Demo Component
  @version 1.2.0
  @description Demo component for showcasing workflow functionality with unified scaling system
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import DemoActiveCard from './components/DemoActiveCard.vue'

// Props
const props = defineProps({
  workflowStore: {
    type: Object,
    required: true
  },
  // Unified scaling object from useScalingV2
  scaling: {
    type: Object,
    required: true
  }
})

// State
const selectedTemplate = ref(null)
const showCreateForm = ref(false)

// Computed properties with error handling
const availableTemplates = computed(() => {
  try {
    return props.workflowStore?.builtInTemplates || []
  } catch (error) {
    console.warn('Could not access builtInTemplates:', error)
    return []
  }
})

const activeWorkflows = computed(() => {
  try {
    return props.workflowStore?.activeWorkflows || []
  } catch (error) {
    console.warn('Could not access activeWorkflows:', error)
    return []
  }
})

const completedWorkflows = computed(() => {
  try {
    return props.workflowStore?.completedWorkflows || []
  } catch (error) {
    console.warn('Could not access completedWorkflows:', error)
    return []
  }
})

const workflowInstances = computed(() => {
  try {
    return props.workflowStore?.workflows || []
  } catch (error) {
    console.warn('Could not access workflows:', error)
    return []
  }
})

// Methods
const selectTemplate = (template) => {
  selectedTemplate.value = template
  showCreateForm.value = true
}

const createWorkflow = async () => {
  if (!selectedTemplate.value) return
  
  try {
    // Check if methods exist before calling them
    if (typeof props.workflowStore?.createWorkflowInstance === 'function') {
      const instance = props.workflowStore.createWorkflowInstance(selectedTemplate.value.id, {
        created_by: 'demo_user'
      })
      
      // Start the workflow if method exists
      if (typeof props.workflowStore?.startWorkflow === 'function') {
        await props.workflowStore.startWorkflow(instance.id)
      }
      
      // Reset form
      showCreateForm.value = false
      selectedTemplate.value = null
      
      console.log('Workflow created and started:', instance)
    } else {
      console.warn('createWorkflowInstance method not available on workflow store')
    }
  } catch (error) {
    console.error('Failed to create workflow:', error)
  }
}

const closeForm = () => {
  showCreateForm.value = false
  selectedTemplate.value = null
}

// Debug: Log store properties on mount
onMounted(() => {
  console.log('WorkflowDemo mounted, store properties:', {
    builtInTemplates: props.workflowStore?.builtInTemplates?.length || 0,
    activeWorkflows: props.workflowStore?.activeWorkflows?.length || 0,
    completedWorkflows: props.workflowStore?.completedWorkflows?.length || 0,
    workflows: props.workflowStore?.workflows?.length || 0,
    availableMethods: Object.keys(props.workflowStore || {}).filter(key => typeof props.workflowStore[key] === 'function')
  })
})
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div :style="{ marginBottom: scaling.space.large }" class="text-center">
      <h1 :style="{ fontSize: scaling.font.pageTitle }" class="font-bold text-gray-900 dark:text-white mb-4">
        Workflow Management Demo
      </h1>
      <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
        Experience the power of our workflow management system with this interactive demo.
        Create workflows, monitor progress, and see how everything works together.
      </p>
    </div>

    <!-- Quick Stats -->
    <div :style="{ marginBottom: scaling.space.large }" class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-blue-600 dark:text-blue-400 mb-2">
          {{ availableTemplates.length }}
        </div>
        <div :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Available Templates</div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-green-600 dark:text-green-400 mb-2">
          {{ activeWorkflows.length }}
        </div>
        <div :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Active Workflows</div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-purple-600 dark:text-purple-400 mb-2">
          {{ completedWorkflows.length }}
        </div>
        <div :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Completed</div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div :style="{ fontSize: scaling.font.heading }" class="font-bold text-orange-600 dark:text-orange-400 mb-2">
          {{ workflowInstances.length }}
        </div>
        <div :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Total Instances</div>
      </div>
    </div>

    <!-- Template Selection -->
    <div :style="{ marginBottom: scaling.space.medium }" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8">
      <h2 :style="{ fontSize: scaling.font.heading, marginBottom: scaling.space.small }" class="font-bold text-gray-900 dark:text-white text-center">
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
            
            <h3 :style="{ fontSize: scaling.font.title }" class="font-semibold text-gray-900 dark:text-white mb-2">
              {{ template.name }}
            </h3>
            
            <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 mb-4">
              {{ template.description }}
            </p>
            
            <div :style="{ fontSize: scaling.font.caption }" class="flex justify-between text-gray-500 dark:text-gray-400 mb-4">
              <span>{{ template.steps?.length || 0 }} steps</span>
              <span>{{ template.module || 'General' }}</span>
            </div>
            
            <button 
              :style="scaling.button"
              class="w-full text-white rounded-lg hover:bg-blue-700 transition-colors">
              Create Workflow
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Workflows Component -->
    <DemoActiveCard 
      :active-workflows="activeWorkflows"
      :scaling="scaling"
    />

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
          <h3 :style="{ fontSize: scaling.font.title }" class="font-semibold text-gray-900 dark:text-white">
            Create New Workflow
          </h3>
          <button
            @click="closeForm"
            :style="{ fontSize: scaling.icon.small }"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="space-y-4">
          <div>
            <label :style="{ fontSize: scaling.font.label }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Template
            </label>
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <h4 :style="{ fontSize: scaling.font.subtitle }" class="font-medium text-gray-900 dark:text-white">{{ selectedTemplate.name }}</h4>
              <p :style="{ fontSize: scaling.font.caption }" class="text-gray-600 dark:text-gray-400">{{ selectedTemplate.description }}</p>
            </div>
          </div>
          
          <div>
            <label :style="{ fontSize: scaling.font.label }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Steps
            </label>
            <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">{{ selectedTemplate.steps?.length || 0 }} steps</p>
          </div>
          
          <div>
            <label :style="{ fontSize: scaling.font.label }" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">
              Module
            </label>
            <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">{{ selectedTemplate.module || 'General' }}</p>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="closeForm"
            :style="scaling.button"
            class="text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="createWorkflow"
            :style="scaling.button"
            class="bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Create Workflow
          </button>
        </div>
      </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
      <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-blue-900 dark:text-blue-300 mb-3">
        <i class="fas fa-info-circle mr-2"></i>
        How to Use This Demo
      </h3>
      <div :style="{ fontSize: scaling.font.body }" class="text-blue-800 dark:text-blue-200 space-y-2">
        <p>1. <strong>Choose a Template:</strong> Click on any workflow template above to get started</p>
        <p>2. <strong>Create Workflow:</strong> Fill out the form and create your first workflow instance</p>
        <p>3. <strong>Monitor Progress:</strong> Watch as your workflow progresses through its steps</p>
        <p>4. <strong>Complete Steps:</strong> Interact with the workflow to complete individual steps</p>
        <p>5. <strong>Track Results:</strong> See the final outcome and workflow completion</p>
      </div>
    </div>
  </div>
</template>