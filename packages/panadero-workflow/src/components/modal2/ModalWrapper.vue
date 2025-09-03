<!--
  Modal Wrapper Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Pure layout wrapper for workflow modal - handles layout, events, and data passing only
-->
<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import ModalHeader from './ModalHeader.vue'
import ModalFooter from './ModalFooter.vue'
import WorkflowStepper from './WorkflowStepper.vue'
import WorkflowStepInfo from './WorkflowStepInfo.vue'
import ModalCurrentStep from './ModalCurrentStep.vue'


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
const viewedStep = ref(0) // UI state for viewing steps (Previous/Next buttons)

// Computed
const hasConfigWorkflow = computed(() => {
  return props.activeWorkflow?.configWorkflow || props.activeWorkflow?.template?._source === 'config_file'
})

const workflowSteps = computed(() => {
  return props.activeWorkflow?.steps || props.activeWorkflow?.template?.steps || []
})

const currentStep = computed(() => {
  return props.activeWorkflow?.currentStep || 0
})

// Initialize viewedStep to currentStep when workflow changes
watch(() => props.activeWorkflow?.currentStep, (newStep) => {
  if (newStep !== undefined) {
    viewedStep.value = newStep
  }
}, { immediate: true })

// Methods
function closeModal() {
  emit('close')
  activeTab.value = 'steps' // Reset to default tab
}

function handleTabChange(tabId) {
  activeTab.value = tabId
}

// Navigation methods for viewing steps (Previous/Next buttons)
function goToPreviousStep() {
  if (viewedStep.value > 0) {
    viewedStep.value--
  }
}

function goToNextStep() {
  if (viewedStep.value < workflowSteps.value.length - 1) {
    viewedStep.value++
  }
}

// Handle step data updates - pass through to store
const handleStepDataUpdated = (eventData) => {
  console.debug('🔵 MODAL WRAPPER: handleStepDataUpdated called with:', eventData)
  
  try {
    const { stepIndex, data } = eventData
    
    if (props.activeWorkflow && props.workflowStore) {
      const workflowId = props.activeWorkflow.instanceId
      const workflow = props.workflowStore.workflows.find(w => w.id === workflowId)
      
      if (workflow && workflow.steps && workflow.steps[stepIndex]) {
        if (!workflow.steps[stepIndex].data) {
          workflow.steps[stepIndex].data = {}
        }
        Object.assign(workflow.steps[stepIndex].data, data)
        console.debug('🔵 SUCCESSFULLY updated step data:', workflow.steps[stepIndex])
      }
    }
  } catch (error) {
    console.error('🔴 Error updating step data:', error)
  }
}

// Lifecycle
onMounted(() => {
  console.debug('ModalWrapper mounted')
})
</script>

<template>
  <!-- Only render when activeWorkflow exists -->
  <div v-if="show && activeWorkflow" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-hidden" @click="closeModal">
    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col overflow-hidden" 
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
          
          <!-- Column 1 Content -->
          <div class="flex-1 overflow-y-auto p-2">

            <WorkflowStepper 
              :workflow-steps="workflowSteps"
              :current-step="currentStep"
              :viewed-step="viewedStep"
              :scaling="scaling"
            />
          </div>
        </div>

        <!-- COLUMN 2: Middle - Step Info (25%) -->
        <div class="w-full lg:w-1/4 flex flex-col border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800 overflow-hidden" 
             style="min-width: 0; flex-shrink: 0; flex-basis: 25%;">
          <div class="flex-1 overflow-y-auto p-2">
          
            <!-- Column 2 Content -->
            <WorkflowStepInfo 
              :workflow-steps="workflowSteps"
              :current-step="currentStep"
              :viewed-step="viewedStep"
              :scaling="scaling"
              :active-workflow="activeWorkflow"
            />
            </div>
        </div>

        <!-- COLUMN 3: Right Side - Current Step Content (50%) -->
        <div class="w-full lg:w-1/2 flex flex-col overflow-hidden min-h-0 bg-gray-50 dark:bg-gray-800" 
             style="min-width: 0; flex-shrink: 0; flex-basis: 50%;">
          
          <!-- Column 3 Content -->
          <div class="flex-1 overflow-y-auto p-2">
            <!-- Step Content Only - No Duplicate Headers or Info -->
            <ModalCurrentStep 
              :workflow="activeWorkflow"  
              :workflow-store="workflowStore"
              :current-step="currentStep"
              :viewed-step="viewedStep"
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
        :current-step="currentStep"
        :viewed-step="viewedStep"
        :scaling="scaling"
        @previous-step="goToPreviousStep"
        @next-step="goToNextStep"
      />
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
