<!--
  Workflow Modal Component
  @version 1.3.8
  @date 31-Aug-2025
  @description Modal for displaying detailed workflow information - orchestrates modular components with unified scaling
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
//import { useWorkflowStore } from '../composables/workflowStore.js'
import ModalHeader from './ModalHeader.vue'
import ModalTabNavigation from './ModalTabNavigation.vue'
import ModalStepsContent from './ModalStepsContent.vue'
import ModalConfigContent from './ModalConfigContent.vue'
import ModalHistoryContent from './ModalHistoryContent.vue'
import ModalCurrentStep from './ModalCurrentStep.vue'
import ModalFooter from './ModalFooter.vue'
import WorkflowInfo from './ModalInfo.vue'

import StepInfoPanel from './StepInfoPanel.vue'

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
  },
  // Unified scaling object from useScalingV2
  scaling: {
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

// Get step status for display
function getStepStatus(stepIndex) {
  if (stepIndex < currentStep.value) return 'Completed'
  if (stepIndex === currentStep.value) return 'Active'
  return 'Pending'
}

// Get step type display
function getStepTypeDisplay(stepIndex) {
  const step = workflowSteps.value[stepIndex]
  if (!step) return 'Unknown'
  
  switch (step.type) {
    case 'shared_entity_selection':
      return 'Entity Selection Required'
    case 'form_submission':
      return 'Form Submission'
    case 'checklist':
      return 'Checklist'
    case 'approval':
      return 'Approval Required'
    default:
      return step.type || 'Unknown'
  }
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
         style="width: 95vw; height: 90vh; max-width: 95vw; max-height: 90vh;" 
         @click.stop>
      
      <!-- Modal Header (fixed) -->
      <ModalHeader 
        :active-workflow="activeWorkflow"
        :has-config-workflow="hasConfigWorkflow"
        :scaling="scaling"
        @close="closeModal"
      />

      <!-- THREE COLUMN LAYOUT - 25% | 25% | 50% -->
      <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0">
        
        <!-- COLUMN 1: Left Side - Vertical Stepper (25%) -->
        <div class="w-full lg:w-1/4 flex flex-col border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 overflow-hidden" 
             style="min-width: 0; flex-shrink: 0; flex-basis: 25%;">
          
          <!-- Minimal Stepper Header -->
          <div class="p-3 border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
            <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white mb-2">
              Workflow steps {{ currentStep + 1 }}/{{ workflowSteps.length }}
            </h3>
          </div>

          <!-- Vertical Stepper with Progress Bars -->
          <div class="flex-1 overflow-y-auto p-4">
            <nav aria-label="Workflow Progress">
              <ol role="list" class="overflow-hidden">
                <li v-for="(step, stepIdx) in workflowSteps" :key="stepIdx" 
                    :class="[stepIdx !== workflowSteps.length - 1 ? 'pb-3' : '', 'relative']">
                  
                  <!-- Progress Line -->
                  <div v-if="stepIdx !== workflowSteps.length - 1" 
                       class="absolute top-3 left-3 mt-0.5 -ml-px h-full w-0.5"
                       :class="stepIdx < currentStep ? 'bg-indigo-600 dark:bg-indigo-500' : 'bg-gray-300 dark:bg-gray-800'"
                       aria-hidden="true" />
                  
                  <!-- Step Content -->
                  <div class="group relative flex items-start">
                    <!-- Step Circle -->
                    <span class="flex h-6 items-center">
                      <span class="relative z-10 flex size-6 items-center justify-center rounded-full"
                            :class="stepIdx < currentStep ? 'bg-indigo-600 group-hover:bg-indigo-800 dark:bg-indigo-500 dark:group-hover:bg-indigo-600' : 
                                   stepIdx === currentStep ? 'border-2 border-indigo-600 bg-white dark:border-indigo-500 dark:bg-gray-900' : 
                                   'border-2 border-gray-300 bg-white group-hover:border-gray-400 dark:border-white/15 dark:bg-gray-900 dark:group-hover:border-white/25'">
                        
                        <!-- Check Icon for completed steps -->
                        <i v-if="stepIdx < currentStep" class="fas fa-check text-white text-xs"></i>
                        
                        <!-- Current step indicator -->
                        <span v-else-if="stepIdx === currentStep" 
                              class="size-1.5 rounded-full bg-indigo-600 dark:bg-indigo-500"></span>
                        
                        <!-- Upcoming step indicator -->
                        <span v-else class="size-1.5 rounded-full bg-transparent group-hover:bg-gray-300 dark:group-hover:bg-white/15"></span>
                      </span>
                    </span>
                    
                    <!-- Step Info -->
                    <span class="ml-3 flex min-w-0 flex-col">
                      <span class="text-xs font-medium"
                            :class="stepIdx < currentStep ? 'text-gray-900 dark:text-white' : 
                                   stepIdx === currentStep ? 'text-indigo-600 dark:text-indigo-400' : 
                                   'text-gray-500 dark:text-gray-400'">
                        {{ step.name || `Step ${stepIdx + 1}` }}
                      </span>
                      <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ step.description || 'Step description' }}
                      </span>
                    </span>
                  </div>
                </li>
              </ol>
            </nav>
          </div>





        </div>

        <!-- COLUMN 2: Middle - Step Info (25%) -->
        <div class="w-full lg:w-1/4 flex flex-col border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 overflow-hidden" 
             style="min-width: 0; flex-shrink: 0; flex-basis: 25%;">
          
          <!-- Step Info Header -->
          <div class="p-3 border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
            <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white mb-2">
              Step Information
            </h3>
          </div>

          <!-- Step Info Content -->
          <div class="flex-1 overflow-y-auto p-4">
            <div class="space-y-4">
              <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                <h4 :style="{ fontSize: scaling.font.caption }" class="font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Current Step
                </h4>
                <div :style="{ fontSize: scaling.font.body }" class="text-gray-900 dark:text-white font-medium">
                  {{ workflowSteps[currentStep]?.name || `Step ${currentStep + 1}` }}
                </div>
              </div>
              
              <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                <h4 :style="{ fontSize: scaling.font.caption }" class="font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <i class="fas fa-info-circle mr-1"></i>
                  Description
                </h4>
                <div :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">
                  {{ workflowSteps[currentStep]?.description || 'Step description' }}
                </div>
              </div>
              
              <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                <h4 :style="{ fontSize: scaling.font.caption }" class="font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <i class="fas fa-tasks mr-1"></i>
                  Step Type
                </h4>
                <div :style="{ fontSize: scaling.font.body }" class="text-gray-900 dark:text-white font-medium">
                  {{ getStepTypeDisplay(currentStep) }}
                </div>
              </div>
              
              <!-- Dynamic Step Information Based on Type -->
              <StepInfoPanel 
                v-if="workflowSteps[currentStep]"
                :step="workflowSteps[currentStep]"
                :scaling="scaling"
              />
              
              <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                <h4 :style="{ fontSize: scaling.font.caption }" class="font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Progress
                </h4>
                <div :style="{ fontSize: scaling.font.title }" class="text-blue-600 dark:text-blue-400 font-bold">
                  {{ workflowSteps.length > 0 ? Math.round(((currentStep + 1) / workflowSteps.length) * 100) : 0 }}%
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- COLUMN 3: Right Side - Current Step Content (50%) -->
        <div class="w-full lg:w-1/2 flex flex-col overflow-hidden min-h-0 bg-white dark:bg-gray-800" 
             style="min-width: 0; flex-shrink: 0; flex-basis: 50%;">
          
          <!-- Current Step Content - Pure Content, No Duplication -->
          <div class="flex-1 overflow-y-auto p-4">
            <!-- Step Content Only - No Duplicate Headers or Info -->
            <ModalCurrentStep 
              :workflow="activeWorkflow"  
              :workflow-store="workflowStore"
              :current-step="currentStep"
              :scaling="scaling"
              @step-data-updated="handleStepDataUpdated"
            />
          </div>
        </div>
      </div>

      <!-- Modal Footer (fixed) -->
      <ModalFooter 
        :workflow-steps="workflowSteps" 
        :active-workflow="activeWorkflow"
        :scaling="scaling"
      />
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
