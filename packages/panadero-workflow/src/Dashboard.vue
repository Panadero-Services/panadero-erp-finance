<!--
  Financial ERP Workflow Dashboard
  @version 1.0.9
  @date 22-Aug-2025
  @description Enhanced workflow management interface with modular components
-->
<script setup>
import { onMounted, computed } from 'vue'
import { useWorkflowDashboard } from './composables/useWorkflowDashboard.js'
import { useWorkflowSettings } from './composables/useWorkflowSettings.js'
import { useWorkflowStore } from './composables/workflowStore.js'
import WorkflowCard from './components/Card.vue'
import ActiveWorkflowCard from './components/ActiveCard.vue'
import WorkflowModal from './components/Modal.vue'
import WorkflowDetailModal from './components/DetailModal.vue'
import WorkflowStatistics from './components/Statistics.vue'

// Props
const props = defineProps({
  showStats: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['workflow-selected', 'workflow-started'])

// Stores
const settings = useWorkflowSettings()
const workflowStore = useWorkflowStore()

// Dynamic font sizes (same as Statistics.vue)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _gridLayout = computed(() => { 
      if (settings.fontSizes.value.base < 10) return `grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-2`;
      if (settings.fontSizes.value.base < 15) return `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4`;
      return `grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6`;
 });




  // Use the workflow dashboard composable
  const {
    // State
    selectedWorkflow,
    showOverlay,
    activeWorkflows,
    selectedActiveWorkflow,
    showWorkflowModal,
    configWorkflows,
    loadingConfigWorkflows,
    
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
    getModuleDisplayName
  } = useWorkflowDashboard()

// Lifecycle
onMounted(() => {
  console.debug('WorkflowDashboard mounted')
  console.debug('Store object:', workflowStore)
  console.debug('Available templates:', workflowStore.builtInTemplates)
  console.debug('Config workflows:', configWorkflows)
  // console.log('Workflow config:', workflowConfig) // Removed - no longer available
})




</script>

<template>
  <div class="py-6">

    <!-- Active Workflows -->
    <div v-if="activeWorkflows.length > 0" class=" ">
      <h2 :style="{ fontSize: _value }" class="text-gray-500 mt-6 mb-3">
          <i class="fas fa-play-circle mr-2 text-green-600 "></i>
          Active Workflowsz ({{ activeWorkflows.length }})
      </h2>
      <div :class="_gridLayout">
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
    <div v-if="loadingConfigWorkflows || (!workflowModules.length)" class="text-center ">
      <div class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
              <p :style="{ fontSize: _value }" class="text-gray-600 dark:text-gray-300">
        {{ loadingConfigWorkflows ? 'Loading workflows from config files...' : 'Loading workflows...' }}
      </p>
              <div :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400 mt-2 space-y-1">
        <p>Config workflows: {{ configWorkflows?.length || 0 }}</p>
        <p>Store templates: {{ workflowStore.builtInTemplates?.length || 0 }}</p>
        <p v-if="configWorkflows?.length > 0" class="text-green-600 dark:text-green-400">
          <i class="fas fa-check mr-1"></i>
          Loaded from config files
        </p>
      </div>
    </div>

    <!-- Workflow Templates -->
    <div v-else>
            <h2 :style="{ fontSize: _value }" class="text-gray-500 mt-8 mb-3">
          <i class="fas fa-play-circle mr-2 text-blue-600 "></i>
          List of Workflowss ({{ allWorkflows.length }})
      </h2>
      <div :class="_gridLayout">
        <WorkflowCard 
          v-for="workflow in allWorkflows" 
          :key="workflow.id"
          :workflow="workflow"
          :clickable="false"
        >
          <!-- Custom actions slot -->
          <template #actions>

            <div class="flex space-x-2 justify-end">
              <button 
                @click="startWorkflowDirectly(workflow, emit)"
                class="px-4 py-2 text-blue-700 dark:text-blue-300 border-2 border-blue-500 dark:border-blue-600 rounded-lg hover:bg-blue-500 dark:hover:bg-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :style="{ fontSize: _caption }">
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
            <h3 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
              Start Workflow
            </h3>
            <p :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">
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
            <p :style="{ fontSize: _caption }" class="text-gray-600 dark:text-gray-300">
              {{ selectedWorkflow?.description || 'No description available' }}
            </p>
                 </div>
                 
                      <div :style="{ fontSize: _caption }" class="grid grid-cols-2 gap-4">
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

        <!-- Enhanced Workflow Detail Modal -->
    <WorkflowDetailModal 
      :show="false"
      :activeWorkflow="selectedActiveWorkflow"
      @close="closeWorkflowModal"
    />
    
    <!-- Fallback Workflow Modal -->
    <WorkflowModal 
      v-if="showWorkflowModal"
      :show="showWorkflowModal"
      :activeWorkflow="selectedActiveWorkflow"
      @close="closeWorkflowModal"
    />
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
