<!--
  Workflow Modal Component
  @version 1.1.0
  @date 22-Aug-2025
  @description Modal for displaying detailed workflow information - orchestrates modular components
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWorkflowStore } from '../composables/workflowStore.js'
import ModalHeader from './ModalHeader.vue'
import ModalTabNavigation from './ModalTabNavigation.vue'
import ModalStepsContent from './ModalStepsContent.vue'
import ModalConfigContent from './ModalConfigContent.vue'
import ModalHistoryContent from './ModalHistoryContent.vue'
import ModalCurrentStep from './ModalCurrentStep.vue'
import ModalFooter from './ModalFooter.vue'
import WorkflowInfo from './modal/ModalInfo.vue'

// Stores and composables
// const workflowStore = useWorkflowStore() // This line is removed as per the edit hint

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  activeWorkflow: {
    type: Object,
    default: null
  },
  workflowStore: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['close'])

// State
const activeTab = ref('steps')

// Computed
const hasConfigWorkflow = computed(() => {
  return props.activeWorkflow?.configWorkflow || props.activeWorkflow?.template?._source === 'config_file'
})

const workflowSteps = computed(() => {
  return props.activeWorkflow?.steps || props.activeWorkflow?.template?.steps || []
})

const currentStep = computed(() => {
  return props.activeWorkflow?.currentStep || 0  // ← Return the INDEX, not the step object
})

// Tab management
const leftTabs = [
  { id: 'steps', name: 'All Steps', icon: 'fas fa-list-ol' },
  { id: 'info', name: 'Information', icon: 'fas fa-info-circle' },
  { id: 'config', name: 'Config Details', icon: 'fas fa-cog' },
  { id: 'history', name: 'History', icon: 'fas fa-history' }
]

// Methods
function closeModal() {
  emit('close')
  activeTab.value = 'steps' // Reset to default tab
}

function handleTabChange(tabId) {
  activeTab.value = tabId
}

// Handle step data updates
const handleStepDataUpdated = (eventData) => {
  console.debug('🔵 MODAL: handleStepDataUpdated called with:', eventData)
  console.debug('🔵 props.activeWorkflow exists:', !!props.activeWorkflow)
  console.debug('🔵 props.workflowStore exists:', !!props.workflowStore)
  
  try {
    const { stepIndex, data } = eventData
    
    // Update the workflow step data in the store
    if (props.activeWorkflow && props.workflowStore) {
      console.debug('🔵 Both props exist, proceeding...')
      
      // Find the workflow in the store
      const workflowId = props.activeWorkflow.instanceId
      console.debug('🔵 workflowId:', workflowId)
      
      const workflow = props.workflowStore.workflows.find(w => w.id === workflowId)
      console.debug('🔵 workflow found:', !!workflow)
      console.debug('🔵 workflow.steps exists:', !!workflow?.steps)
      console.debug('🔵 stepIndex:', stepIndex)
      console.debug('🔵 workflow.steps[stepIndex] exists:', !!workflow?.steps?.[stepIndex])
      
      if (workflow && workflow.steps && workflow.steps[stepIndex]) {
        console.debug('🔵 All conditions met, updating step data...')
        
        // Update the step data
        if (!workflow.steps[stepIndex].data) {
          workflow.steps[stepIndex].data = {}
        }
        Object.assign(workflow.steps[stepIndex].data, data)
        
        console.debug('🔵 SUCCESSFULLY updated step data:', workflow.steps[stepIndex])
      } else {
        console.debug('🔴 Could not find workflow or step:', {
          workflowFound: !!workflow,
          stepsExist: !!workflow?.steps,
          stepExists: !!workflow?.steps?.[stepIndex],
          totalSteps: workflow?.steps?.length
        })
      }
    } else {
      console.debug('🔴 Missing props:', {
        activeWorkflow: !!props.activeWorkflow,
        workflowStore: !!props.workflowStore
      })
    }
  } catch (error) {
    console.error('🔴 Error updating step data:', error)
  }
}

// Lifecycle
onMounted(() => {
  console.debug('Modal mounted')
})
</script>

<template>
  <!-- FIXED: Only render when activeWorkflow exists -->
  <div v-if="show && activeWorkflow" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-hidden" @click="closeModal">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col overflow-hidden" 
         style="width: 80vw; height: 80vh; max-width: 90vw; max-height: 90vh;" 
         @click.stop>
      
      <!-- Modal Header (fixed) -->
      <ModalHeader 
        :active-workflow="activeWorkflow"
        :has-config-workflow="hasConfigWorkflow"
        @close="closeModal"
      />

      <!-- Two Column Layout - Responsive (scrollable) -->
      <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0">
        
        <!-- Column A: Left Side (40% on large screens, full width on small) -->
        <div class="w-full lg:w-2/5 flex flex-col border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 overflow-hidden" 
             style="min-width: 0; flex-shrink: 0; flex-basis: 40%;">
          
          <!-- Tab Navigation (fixed) -->
          <ModalTabNavigation 
            :active-tab="activeTab"
            :left-tabs="leftTabs"
            @tab-change="handleTabChange"
          />

          <!-- Tab Content (scrollable) -->
          <div class="flex-1 overflow-y-auto min-h-0">
            <!-- Steps Tab -->
            <div v-if="activeTab === 'steps'" class="h-full">
              <ModalStepsContent 
                :workflow-steps="workflowSteps"
                :active-workflow="activeWorkflow"
                :workflow-store="workflowStore"
              />
            </div>

            <!-- Information Tab -->
            <div v-if="activeTab === 'info'" class="p-2 sm:p-4">
              <WorkflowInfo :workflowData="activeWorkflow" />
            </div>

            <!-- Config Tab -->
            <div v-if="activeTab === 'config'">
              <ModalConfigContent 
                :active-workflow="activeWorkflow"
              />
            </div>

            <!-- History Tab -->
            <div v-if="activeTab === 'history'">
              <ModalHistoryContent 
                :active-workflow="activeWorkflow"
              />
            </div>
          </div>
        </div>

        <!-- Column B: Current Step (scrollable) -->
        <div class="flex-1 flex flex-col overflow-hidden min-h-0">
          <div class="flex-1 overflow-y-auto p-4">
            <ModalCurrentStep 
              :workflow="activeWorkflow"  
              :workflow-store="workflowStore"
              :current-step="currentStep"
              @step-data-updated="handleStepDataUpdated"
            />
          </div>
        </div>
      </div>

      <!-- Modal Footer (fixed) -->
      <ModalFooter 
        :workflow-steps="workflowSteps" 
        :active-workflow="activeWorkflow"
      />
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
