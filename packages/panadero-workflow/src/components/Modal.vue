<!--
  Workflow Modal Wrapper Component
  @version 1.0.2
  @date 22-Aug-2025
  @description Modal wrapper for workflow management based on WorkflowDetailModal layout
-->
<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import { useWorkflowStore } from '../composables/workflowStore.js'
// REMOVED: import { useWorkflowConfig } from '../../../panadero-erp-finance/src/composables/useWorkflowConfig.js'
import WorkflowStepper from './WorkflowStepper.vue'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowInfo from './Info.vue'

// Stores
const settings = useWorkflowSettings()
const workflowStore = useWorkflowStore()

// Dynamic font sizes (same as Statistics.vue and Dashboard.vue)
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _windowSize = computed(() => { return `width: ${settings.fontSizes.value.base*5}vw; height: ${settings.fontSizes.value.base*7}vh; max-width: 95vw; max-height: 95vh;`; });

// Debug settings
console.debug('Modal.vue - settings:', settings)
console.debug('Modal.vue - fontSizesComputed:', settings.fontSizesComputed)
console.debug('Modal.vue - h1 size:', settings.fontSizesComputed.value.h1)
// REMOVED: const workflowConfig = useWorkflowConfig()

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  activeWorkflow: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close'])

// State
const workflowDetails = ref(null)
const loadingDetails = ref(false)
const activeTab = ref('information') // 'information', 'config-details', 'history'
const showCurrentStepOnMobile = ref(false)

// Computed
const hasConfigWorkflow = computed(() => {
  return props.activeWorkflow && (props.activeWorkflow.id || props.activeWorkflow.workflowNr)
})

const workflowMetadata = computed(() => {
  if (!props.activeWorkflow) return null
  
  // Use active workflow data directly
  return {
    name: props.activeWorkflow.name || 'Unknown Workflow',
    description: props.activeWorkflow.description || 'No description available',
    module: props.activeWorkflow.module || 'general',
    category: props.activeWorkflow.category || 'general',
    estimated_duration: props.activeWorkflow.estimated_duration || 'Unknown',
    complexity: props.activeWorkflow.complexity || 'medium'
  }
})

const workflowSteps = computed(() => {
  if (!props.activeWorkflow) return []
  return props.activeWorkflow.steps || props.activeWorkflow.template?.steps || []
})

const currentStep = computed(() => {
  if (!workflowSteps.value || workflowSteps.value.length === 0) return null
  return workflowSteps.value[workflowStore.workflowExecution.currentStep] || null
})

// Methods
async function loadWorkflowDetails() {
  if (!props.activeWorkflow) return
  
  loadingDetails.value = true
  
  try {
    // Initialize workflow state
    workflowStore.workflowExecution.currentStep = 0
    workflowStore.workflowExecution.stepData = {}
    workflowStore.workflowExecution.selectedVendor = null
    
    // Use active workflow data directly
    workflowDetails.value = props.activeWorkflow
    
    // Reset to first step
    activeTab.value = 'steps'
    showCurrentStepOnMobile.value = false
    
  } catch (error) {
    console.error('Error loading workflow details:', error)
  } finally {
    loadingDetails.value = false
  }
}

function closeModal() {
  emit('close')
}

// Lifecycle
onMounted(() => {
  if (props.show && props.activeWorkflow) {
    loadWorkflowDetails()
  }
})

// Watch for changes
watch(() => props.show, (newShow) => {
  if (newShow && props.activeWorkflow) {
    loadWorkflowDetails()
  }
})

// Watch for activeWorkflow changes
watch(() => props.activeWorkflow, (newWorkflow) => {
  if (newWorkflow && props.show) {
    loadWorkflowDetails()
  }
}, { immediate: true })

// Tab management for Column A (left side)
const leftTabs = [
  { id: 'steps', name: 'All Steps', icon: 'fas fa-list-ol' },
  { id: 'info', name: 'Information', icon: 'fas fa-info-circle' },
  { id: 'config', name: 'Config Details', icon: 'fas fa-cog' },
  { id: 'history', name: 'History', icon: 'fas fa-history' }
]
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="closeModal">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col" 
         :style="_windowSize" 
         @click.stop>
      
      <!-- Modal Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
            <i class="fas fa-cogs text-blue-600 dark:text-blue-400 text-xl"></i>
          </div>
          <div>
            <h2 :style="{ fontSize: _value }" class="font-bold text-gray-900 dark:text-white">
              {{ activeWorkflow?.name || 'Workflow' }}
            </h2>
            <div class="flex items-center space-x-3 mt-1">
              <span :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-hashtag mr-1"></i>{{ activeWorkflow?.workflowNr || activeWorkflow?.id || 'N/A' }}
              </span>
              <span :style="{ fontSize: _caption }" class="px-2 py-1 font-medium bg-green-100 text-green-800 dark:bg-green-900  dark:text-green-400 rounded-full">
                {{ activeWorkflow?.status || 'Active' }}
              </span>
              <span v-if="activeWorkflow?.module" :style="{ fontSize: _caption }" class="px-2 py-1 font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 rounded-full">
                <i class="fas fa-cube mr-1"></i>{{ activeWorkflow.module }}
              </span>
              <span v-if="activeWorkflow?.category" :style="{ fontSize: _caption }" class="px-2 py-1 font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 rounded-full">
                <i class="fas fa-tag mr-1"></i>{{ activeWorkflow.category }}
              </span>
              <span v-if="hasConfigWorkflow" :style="{ fontSize: _caption }" class="px-2 py-1 font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 rounded-full">
                <i class="fas fa-file-code mr-1"></i>Config Source
              </span>
            </div>
          </div>
        </div>
        <button @click="closeModal" :style="{ fontSize: _value }" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Mobile Toggle for Current Step 
      <div class="lg:hidden px-4 py-2 border-b border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700">
        <button
          @click="showCurrentStepOnMobile = !showCurrentStepOnMobile"
          class="w-full py-2 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
          :style="{ fontSize: _caption }"
        >
          <i :class="showCurrentStepOnMobile ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="mr-2"></i>
          {{ showCurrentStepOnMobile ? 'Hide' : 'Show' }} Current Step
        </button>
      </div>-->

      <!-- Two Column Layout - Responsive -->
      <div class="flex-1 flex flex-col lg:flex-row overflow-hidden" style="min-height: 0;">
        
        <!-- Column A: Left Side (40% on large screens, full width on small) -->
        <div class="w-full lg:w-2/5 flex flex-col border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800" 
             :class="{ 'hidden lg:flex': showCurrentStepOnMobile }"
             style="min-width: 0; flex-shrink: 0; flex-basis: 40%;">
          
          <!-- Tab Navigation for Column A -->
          <div class="px-2 sm:px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
            <nav class="flex flex-wrap gap-2 sm:gap-4">
              <button
                v-for="tab in leftTabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'rounded-lg font-medium text-xs sm:text-sm transition-colors flex-shrink-0',
                  activeTab === tab.id
                    ? 'bg-blue-600 text-white'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'
                ]"
                :style="{ fontSize: _caption, padding: _padding }"
              >
                <i :class="tab.icon" class="mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">{{ tab.name }}</span>
                <span class="sm:hidden">{{ tab.name.split(' ')[0] }}</span>
              </button>
            </nav>
          </div>

          <!-- Tab Content for Column A -->
          <div class="flex-1 overflow-hidden">
            
            <!-- All Steps Tab -->
            <div v-if="activeTab === 'steps'" class="h-full overflow-y-auto p-2 sm:p-4">
              <WorkflowStepper 
                v-if="workflowSteps.length > 0"
                :steps="workflowSteps"
                :currentStep="workflowStore.workflowExecution.currentStep"
                @step-click="workflowStore.setCurrentStep"
              />
              <div v-else class="text-center py-16">
                <i class="fas fa-list-ol text-gray-400 text-3xl mb-4"></i>
                <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
                  No workflow steps available
                </p>
              </div>
            </div>

            <!-- Information Tab -->
            <div v-if="activeTab === 'info'" class="h-full overflow-y-auto p-2 sm:p-4">
              <WorkflowInfo :workflowData="activeWorkflow" />
            </div>

            <!-- Config Details Tab -->
            <div v-if="activeTab === 'config'" class="h-full overflow-y-auto p-2 sm:p-4">
              <h3 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-cog mr-2 text-indigo-600"></i>
                Configuration Details
              </h3>
              <div class="text-center py-8">
                <i class="fas fa-cog text-gray-400 text-3xl mb-4"></i>
                <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
                  Configuration details will be loaded dynamically
                </p>
              </div>
            </div>

            <!-- History Tab -->
            <div v-if="activeTab === 'history'" class="h-full overflow-y-auto p-2 sm:p-4">
              <h3 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2 text-indigo-600"></i>
                Workflow History
              </h3>
              <div class="text-center py-8">
                <i class="fas fa-history text-gray-400 text-3xl mb-4"></i>
                <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
                  History will be loaded dynamically
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Column B: Right Side (60% on large screens, full width on small) -->
        <div class="w-full lg:w-3/5 flex flex-col bg-gray-50 dark:bg-gray-700/30" 
             :class="{ 'flex': showCurrentStepOnMobile, 'hidden lg:flex': !showCurrentStepOnMobile }"
             :style="{ fontSize: _value }"
             style="min-width: 0; flex-shrink: 0; flex-basis: 60%;">
          
          <!-- Current Step Header -->
          <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
            <h3 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
              <i class="fas fa-edit mr-2 text-indigo-600 dark:text-indigo-400"></i>
             {{ currentStep?.name ? `Current Step: ${currentStep.name}` : 'Current Step' }}
            </h3>
            <div v-if="currentStep?.type" class="mt-1 text-right">
              <span :style="{ fontSize: _caption }" class=" px-2 py-1 text-gray-700 dark:text-gray-300 rounded">
              Action Type:
              </span>
              <span :style="{ fontSize: _caption }" class=" px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded">
                 {{ currentStep.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
              </span>
              <span v-if="currentStep.estimatedDuration" :style="{ fontSize: _caption }" class="ml-2 text-gray-500 dark:text-gray-400 text-xs">
                <i class="fas fa-clock mr-1"></i>{{ currentStep.estimatedDuration }}
              </span>
            </div>
          </div>

          <!-- Current Step Content -->
          <div class="flex-1 overflow-y-auto p-2 sm:p-4">
            <WorkflowForm 
              v-if="currentStep"
              :step="currentStep"
              :stepData="workflowStore.workflowExecution.stepData"
              @update-step-data="workflowStore.updateStepData"
            />
            <div v-else class="text-center py-16">
              <i class="fas fa-info-circle text-gray-400 text-3xl mb-4"></i>
              <p :style="{ fontSize: _value }" class="text-gray-500 dark:text-gray-400">
                No current step available
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
        <div class="flex justify-between items-center">
          <div class="flex space-x-3">
            <button :style="{ fontSize: _caption }" class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <i class="fas fa-pause mr-2"></i>
              Pause
            </button>
            <button :style="{ fontSize: _caption }" class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-300 dark:border-gray-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
              <i class="fas fa-stop mr-2"></i>
              Cancel
            </button>
          </div>
          <div class="flex space-x-3">
            <button 
              @click="workflowStore.setCurrentStep(Math.max(0, workflowStore.workflowExecution.currentStep - 1))"
              :disabled="workflowStore.workflowExecution.currentStep === 0"
              :style="{ fontSize: _caption }" 
              class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-arrow-left mr-2"></i>
              Previous
            </button>
            <button 
              @click="workflowStore.setCurrentStep(Math.min(workflowSteps.length - 1, workflowStore.workflowExecution.currentStep + 1))"
              :disabled="workflowStore.workflowExecution.currentStep >= workflowSteps.length - 1"
              :style="{ fontSize: _caption }" 
              class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-arrow-right mr-2"></i>
              Next Step
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
