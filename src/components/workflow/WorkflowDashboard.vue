<!--
  Financial ERP Workflow Dashboard
  @version 1.0.8
  @date 18-Aug-2025
  @description Enhanced workflow management interface with modular components
-->
<script setup>
import { onMounted } from 'vue'
import { useWorkflowDashboard } from '../../composables/useWorkflowDashboard.js'
import { useFinanceStore } from '../../stores/financeStore.js'
import WorkflowCard from './WorkflowCard.vue'
import ActiveWorkflowCard from './ActiveWorkflowCard.vue'
import WorkflowModal from './WorkflowModal.vue'
import WorkflowStatistics from '../workflow/WorkflowStatistics.vue'

// Props
const props = defineProps({
  showStats: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['workflow-selected', 'workflow-started'])

// Store
const store = useFinanceStore()

// Use the workflow dashboard composable
const {
  // State
  selectedWorkflow,
  showOverlay,
  activeWorkflows,
  selectedActiveWorkflow,
  showWorkflowModal,
  
  // Computed
  workflowModules,
  allWorkflows,

// Methods
  openWorkflowOverlay,
  closeOverlay,
  openWorkflowModal,
  closeWorkflowModal,
  startWorkflowDirectly,
  deleteActiveWorkflow,
  getModuleDisplayName,
  
  // Store reference
  workflowStore
} = useWorkflowDashboard()

// Lifecycle
onMounted(() => {
  console.log('WorkflowDashboard mounted')
  console.log('Store object:', workflowStore)
  console.log('Available templates:', workflowStore.builtInTemplates)
  console.log('Templates type:', typeof workflowStore.builtInTemplates)
  console.log('Templates length:', workflowStore.builtInTemplates?.length)
})
</script>

<template>
  <div class="p-6">

    <!-- Active Workflows -->
    <div v-if="activeWorkflows.length > 0" class="mb-8">
      <h2 :style="{ fontSize: `${store.fontSizes.base + 6}px` }" class="font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-play-circle mr-2 text-green-600"></i>
        Active Workflows ({{ activeWorkflows.length }})
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <ActiveWorkflowCard 
          v-for="activeWorkflow in activeWorkflows" 
          :key="activeWorkflow.workflowNr"
          :activeWorkflow="activeWorkflow"
          @open="openWorkflowModal"
          @delete="deleteActiveWorkflow"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="!workflowModules.length" class="text-center py-8">
      <div class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <p :style="{ fontSize: `${store.fontSizes.base}px` }" class="text-gray-600 dark:text-gray-300">Loading workflows...</p>
      <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400 mt-2">
        Found {{ workflowStore.builtInTemplates?.length || 0 }} templates in store
      </p>
    </div>

    <!-- Workflow Templates -->
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <WorkflowCard 
          v-for="workflow in allWorkflows" 
          :key="workflow.id"
          :workflow="workflow"
          :clickable="false"
        >
          <!-- Custom actions slot -->
          <template #actions>
            <div class="flex space-x-2">
              <button 
                @click="startWorkflowDirectly(workflow, emit)"
                class="flex-1 px-3 py-2 bg-blue-700 text-white text-sm font-medium rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                :style="{ fontSize: `${store.fontSizes.base - 2}px` }">
                <i class="fas fa-play mr-2"></i>
                Start
              </button>
            </div>
          </template>
        </WorkflowCard>
          </div>
        </div>
        
    <!-- Workflow Start Overlay -->
    <div v-if="showOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeOverlay">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-4xl w-full mx-8 shadow-2xl" @click.stop>
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 :style="{ fontSize: `${store.fontSizes.base + 4}px` }" class="font-semibold text-gray-900 dark:text-white">
              Start Workflow
            </h3>
            <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-500 dark:text-gray-400">
              {{ selectedWorkflow?.name }}
            </p>
               </div>
          <button @click="closeOverlay" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="fas fa-times text-xl"></i>
          </button>
             </div>
            
        <!-- Workflow Details -->
        <div class="mb-6 space-y-3">
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
            <p :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="text-gray-600 dark:text-gray-300">
              {{ selectedWorkflow?.description || 'No description available' }}
            </p>
                 </div>
                 
          <div :style="{ fontSize: `${store.fontSizes.base - 2}px` }" class="grid grid-cols-2 gap-4">
            <div>
              <span class="text-gray-500 dark:text-gray-400">Steps:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ selectedWorkflow?.steps?.length || 0 }}
              </span>
                 </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Duration:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ selectedWorkflow?.estimated_duration || 'N/A' }}
              </span>
                 </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Module:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ getModuleDisplayName(selectedWorkflow?.module) }}
              </span>
            </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Approvals:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-white">
                {{ selectedWorkflow?.steps?.filter(s => s.type === 'approval').length || 0 }}
              </span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-3">
          <button 
            @click="closeOverlay"
            class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancel
          </button>
          <button 
            @click="startWorkflowDirectly(selectedWorkflow, emit)"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-play mr-2"></i>
            Start Workflow
          </button>
        </div>
      </div>
    </div>

        <!-- Workflow Management Modal -->
    <WorkflowModal 
      :show="showWorkflowModal"
      :activeWorkflow="selectedActiveWorkflow"
      @close="closeWorkflowModal"
    />
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
