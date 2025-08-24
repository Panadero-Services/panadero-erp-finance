<!--
  Enhanced Workflow Detail Modal Component
  @version 1.0.8
  @description Modal for displaying detailed workflow information from config files
-->
<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useWorkflowStore } from '../composables/workflowStore.js'
// REMOVED: import { useWorkflowConfig } from '../../../panadero-erp-finance/src/composables/useWorkflowConfig.js'
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'
import WorkflowStepper from './WorkflowStepper.vue'
import WorkflowForm from './WorkflowForm.vue'
import WorkflowInfo from './Info.vue'

// Stores and composables
const workflowStore = useWorkflowStore()
const settings = useWorkflowSettings()

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
const activeTab = ref('steps')
const showCurrentStepOnMobile = ref(false)

// Computed
const hasConfigWorkflow = computed(() => {
  return props.activeWorkflow?.configWorkflow || props.activeWorkflow?.template?._source === 'config_file'
})

const workflowMetadata = computed(() => {
  return workflowDetails.value?._metadata || {}
})

const workflowSteps = computed(() => {
  return workflowDetails.value?.steps || []
})

// Replace currentStepIndex ref with workflowStore
const currentStepIndex = computed(() => workflowStore.workflowExecution.currentStep)
const stepData = computed(() => workflowStore.workflowExecution.stepData)
const sharedEntities = computed(() => workflowStore.workflowExecution.sharedEntities)

const currentStep = computed(() => {
  return workflowSteps.value[currentStepIndex.value]
})

// Helper function to determine if a field should span full width
const isLargeField = (field) => {
  const largeFieldTypes = ['textarea', 'file', 'dynamic_table']
  const largeFieldNames = ['json', 'txt', 'description', 'notes', 'comments', 'configuration', 'raw_data']
  
  // Check field type
  if (largeFieldTypes.includes(field.type)) {
    return true
  }
  
  // Check field name contains large field keywords
  const fieldNameLower = field.name?.toLowerCase() || ''
  return largeFieldNames.some(keyword => fieldNameLower.includes(keyword))
}

// Watch for activeWorkflow changes
watch(() => props.activeWorkflow, async (newWorkflow) => {
  if (newWorkflow && hasConfigWorkflow.value) {
    await loadWorkflowDetails()
  }
}, { immediate: true })

// Methods
async function loadWorkflowDetails() {
  if (!props.activeWorkflow?.template?.id) return
  
  try {
    loadingDetails.value = true
    // TODO: Implement workflow loading logic without external dependencies
    console.log('Workflow loading needs to be implemented')
  } catch (error) {
    console.error('Failed to load workflow details:', error)
  } finally {
    loadingDetails.value = false
  }
}

function closeModal() {
  emit('close')
  activeTab.value = 'steps' // Reset to default tab (All Steps)
  showCurrentStepOnMobile.value = false // Reset mobile view
}

// Tab management for Column A (left side)
const leftTabs = [
  { id: 'steps', name: 'All Steps', icon: 'fas fa-list-ol' },
  { id: 'info', name: 'Information', icon: 'fas fa-info-circle' },
  { id: 'config', name: 'Config Details', icon: 'fas fa-cog' },
  { id: 'history', name: 'History', icon: 'fas fa-history' }
]

// Add after the stores section:
const _caption = computed(() => { return `${settings.fontSizesComputed.value.caption}px`; });
const _value = computed(() => { return `${settings.fontSizesComputed.value.h4}px`; });
const _padding = computed(() => { return `${settings.fontSizesComputed.value.h4/2}px`; });
const _h2 = computed(() => { return `${settings.fontSizesComputed.value.h2}px`; });
const _h3 = computed(() => { return `${settings.fontSizesComputed.value.h3}px`; });
const _body = computed(() => { return `${settings.fontSizesComputed.value.body}px`; });
const _bodySmall = computed(() => { return `${settings.fontSizesComputed.value.bodySmall}px`; });
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="closeModal">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col" 
         style="width: 80vw; height: 80vh; max-width: 90vw; max-height: 90vh;" 
         @click.stop>
      
      <!-- Modal Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
            <i class="fas fa-cogs text-blue-600 dark:text-blue-400 text-xl"></i>
          </div>
          <div>
            <h2 :style="{ fontSize: _h2 }" class="font-bold text-gray-900 dark:text-white">
              {{ workflowDetails?.name || activeWorkflow?.name }}
            </h2>
            <div class="flex items-center space-x-3 mt-1">
              <span :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-hashtag mr-1"></i>{{ activeWorkflow?.workflowNr }}
              </span>
              <span :style="{ fontSize: _bodySmall }" class="px-2 py-1 font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">
                {{ activeWorkflow?.status }}
              </span>
              <span v-if="workflowDetails?.module" :style="{ fontSize: _bodySmall }" class="px-2 py-1 font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 rounded-full">
                <i class="fas fa-cube mr-1"></i>{{ workflowDetails.module }}
              </span>
              <span v-if="workflowDetails?.category" :style="{ fontSize: _bodySmall }" class="px-2 py-1 font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-full">
                <i class="fas fa-tag mr-1"></i>{{ workflowDetails.category }}
              </span>
              <span v-if="hasConfigWorkflow" :style="{ fontSize: _bodySmall }" class="px-2 py-1 font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full">
                <i class="fas fa-file-code mr-1"></i>Config Source
              </span>
            </div>
          </div>
        </div>
        <button @click="closeModal" :style="{ fontSize: _bodySmall }" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Mobile Toggle for Current Step -->
      <div class="lg:hidden px-4 py-2 border-b border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700">
        <button
          @click="showCurrentStepOnMobile = !showCurrentStepOnMobile"
          class="w-full py-2 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
          :style="{ fontSize: _bodySmall }"
        >
          <i :class="showCurrentStepOnMobile ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="mr-2"></i>
          {{ showCurrentStepOnMobile ? 'Hide' : 'Show' }} Current Step
        </button>
      </div>

      <!-- Two Column Layout - Responsive -->
      <div class="flex-1 flex flex-col lg:flex-row overflow-hidden" style="min-height: 0;">
        
        <!-- Column A: Left Side (40% on large screens, full width on small) - Tabs for All Steps, Info, Config, History -->
        <div class="w-full lg:w-2/5 flex flex-col border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800" 
             :class="{ 'hidden lg:flex': showCurrentStepOnMobile }"
             style="min-width: 0; flex-shrink: 0; flex-basis: 40%;"
        >
          <!-- Tab Navigation for Column A -->
          <div class="px-2 sm:px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
            <nav class="flex flex-wrap gap-2 sm:gap-4">
              <button
                v-for="tab in leftTabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'py-2 px-2 sm:px-3 rounded-lg font-medium text-xs sm:text-sm transition-colors flex-shrink-0',
                  activeTab === tab.id
                    ? 'bg-blue-600 text-white'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'
                ]"
                :style="{ fontSize: _bodySmall }"
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
              <div v-if="workflowSteps.length > 0">
                <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
                  <i class="fas fa-list-ol mr-2 text-indigo-600"></i>
                  Workflow Steps ({{ workflowSteps.length }})
                </h3>
                
                <div class="space-y-3">
                  <div v-for="(step, index) in workflowSteps" :key="step.id" class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-3">
                    <div class="flex items-start space-x-3">
                      <div class="flex-shrink-0">
                        <div :class="[
                          'w-7 h-7 rounded-full flex items-center justify-center text-sm font-medium',
                          index === currentStepIndex ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300'
                        ]">
                          {{ index + 1 }}
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white truncate">
                          {{ step.name }}
                        </h4>
                        <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                          {{ step.description }}
                        </p>
                        <div class="flex items-center space-x-2 mt-2">
                          <span :style="{ fontSize: _bodySmall }" class="px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs">
                            {{ step.type }}
                          </span>
                          <span v-if="step.approvalRequired" :style="{ fontSize: _bodySmall }" class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded text-xs">
                            <i class="fas fa-check-circle mr-1"></i>Approval
                          </span>
                          <span v-if="step.estimatedDuration" :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400 text-xs">
                            <i class="fas fa-clock mr-1"></i>{{ step.estimatedDuration }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-16">
                <i class="fas fa-list-ol text-gray-400 text-3xl mb-4"></i>
                <p :style="{ fontSize: _body }" class="text-gray-500 dark:text-gray-400">
                  No workflow steps available
                </p>
              </div>
            </div>

            <!-- Information Tab -->
            <div v-if="activeTab === 'info'" class="h-full overflow-y-auto p-2 sm:p-4">
              <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                Workflow Information
              </h3>
              <WorkflowInfo :workflowData="workflowDetails || activeWorkflow" />
            </div>

            <!-- Config Details Tab -->
            <div v-if="activeTab === 'config'" class="h-full overflow-y-auto p-2 sm:p-4">
              <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-cog mr-2 text-indigo-600"></i>
                Configuration Details
              </h3>
              
              <div class="space-y-4">
                <!-- Metadata -->
                <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                  <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-info mr-2"></i>Metadata
                  </h4>
                  <dl class="space-y-2">
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Version:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowMetadata.version || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Last Updated:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowMetadata.lastUpdated || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Author:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowMetadata.author || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Status:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowMetadata.status || 'N/A' }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Workflow Properties -->
                <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                  <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-cogs mr-2"></i>Properties
                  </h4>
                  <dl class="space-y-2">
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Module:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowDetails?.module || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Category:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowDetails?.category || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Complexity:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowDetails?.complexity || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Duration:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowDetails?.estimated_duration || 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">Approval Levels:</dt>
                      <dd :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">{{ workflowDetails?.approval_levels || 0 }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Raw JSON (for debugging) -->
                <div v-if="workflowDetails" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                  <h4 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-code mr-2"></i>Raw Configuration
                  </h4>
                  <pre :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400 overflow-auto max-h-48 bg-white dark:bg-gray-900 p-2 rounded border">{{ JSON.stringify(workflowDetails, null, 2) }}</pre>
                </div>
              </div>
            </div>

            <!-- History Tab -->
            <div v-if="activeTab === 'history'" class="h-full overflow-y-auto p-2 sm:p-4">
              <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2 text-indigo-600"></i>
                Workflow History
              </h3>
              
              <div class="space-y-3">
                <!-- Show workflow start event if we have start date -->
                <div v-if="activeWorkflow?.startedAt" class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-3">
                  <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <div class="flex-1">
                      <p :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">
                        Workflow started
                      </p>
                      <p :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
                        {{ new Date(activeWorkflow.startedAt).toLocaleString() }}
                      </p>
                    </div>
                  </div>
                </div>
                
                <!-- Show workflow creation event if we have creation metadata -->
                <div v-else-if="workflowMetadata?.created || workflowMetadata?.lastUpdated" class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-3">
                  <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                      <p :style="{ fontSize: _bodySmall }" class="text-gray-900 dark:text-white">
                        Workflow configured
                      </p>
                      <p :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
                        {{ workflowMetadata.lastUpdated || workflowMetadata.created }}
                      </p>
                    </div>
                  </div>
                </div>
                
                <!-- No history available indicator -->
                <div class="text-center py-8">
                  <i class="fas fa-history text-gray-400 text-2xl mb-2"></i>
                  <p :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
                    {{ activeWorkflow?.startedAt ? 'More history items will appear as the workflow progresses' : 'No workflow history available' }}
                  </p>
                  <p v-if="!activeWorkflow?.startedAt" :style="{ fontSize: _bodySmall }" class="text-gray-400 dark:text-gray-500 mt-1">
                    Start the workflow to begin tracking history
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Column B: Right Side (60% on large screens, full width on small) - Current Step (Fixed) -->
        <div class="w-full lg:w-3/5 flex flex-col bg-gray-50 dark:bg-gray-700/30" 
             :class="{ 'flex': showCurrentStepOnMobile, 'hidden lg:flex': !showCurrentStepOnMobile }"
             style="min-width: 0; flex-shrink: 0; flex-basis: 60%;"
        >
          <!-- Current Step Header -->
          <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
            <h3 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white">
              <i class="fas fa-edit mr-2 text-indigo-600"></i>
              {{ currentStep?.name ? `Current Step: ${currentStep.name}` : 'Current Step' }}
            </h3>
            <div v-if="currentStep?.type" class="mt-1">
              <span :style="{ fontSize: _bodySmall }" class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded text-xs">
                {{ currentStep.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
              </span>
              <span v-if="currentStep.estimatedDuration" :style="{ fontSize: _bodySmall }" class="ml-2 text-gray-500 dark:text-gray-400 text-xs">
                <i class="fas fa-clock mr-1"></i>{{ currentStep.estimatedDuration }}
              </span>
            </div>
          </div>

          <!-- Current Step Content -->
          <div class="flex-1 overflow-y-auto p-2 sm:p-4">
            <div v-if="loadingDetails" class="flex items-center justify-center h-64">
              <div class="animate-spin h-8 w-8 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
            </div>
            
            <div v-else-if="currentStep">
              <h4 :style="{ fontSize: _h3 }" class="font-semibold text-gray-900 dark:text-white mb-3">
                {{ currentStep.name }}
              </h4>
              <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400 mb-4">
                {{ currentStep.description }}
              </p>
              
              <!-- Form Schema Display with 2-Column Layout -->
              <div v-if="currentStep.formSchema" class="space-y-4">
                <div v-for="section in currentStep.formSchema.sections" :key="section.title" class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                  <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-4">
                    {{ section.title }}
                  </h5>
                  
                  <!-- Form Fields Grid Layout -->
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div v-for="field in section.fields" :key="field.name" 
                         :class="[
                           'space-y-2',
                           isLargeField(field) ? 'lg:col-span-2' : 'lg:col-span-1'
                         ]">
                      
                      <!-- Field Label -->
                      <label :style="{ fontSize: _bodySmall }" 
                             class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ field.label }}
                        <span v-if="field.required" class="text-red-500">*</span>
                        <span v-if="isLargeField(field)" :style="{ fontSize: _bodySmall }" class="ml-2 text-xs text-blue-600 dark:text-blue-400">
                          (Large Field)
                        </span>
                      </label>
                      
                      <!-- Form Input Based on Type -->
                      <div class="relative">
                        <!-- Text, Email, Tel inputs -->
                        <input v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel'" 
                               :type="field.type"
                               :placeholder="field.placeholder || `Enter ${field.label.toLowerCase()}`"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               :style="{ fontSize: _bodySmall }"
                        />
                        
                        <!-- Textarea -->
                        <textarea v-else-if="field.type === 'textarea'" 
                                  :placeholder="field.placeholder || `Enter ${field.label.toLowerCase()}`"
                                  :rows="field.attributes?.rows || 4"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
                                  :style="{ fontSize: _bodySmall }"
                        ></textarea>
                        
                        <!-- Select Dropdown -->
                        <select v-else-if="field.type === 'select'" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                :style="{ fontSize: _bodySmall }">
                          <option value="">Select {{ field.label.toLowerCase() }}</option>
                          <option v-for="option in field.options" :key="option" :value="option">
                            {{ option }}
                          </option>
                        </select>
                        
                        <!-- Date Input -->
                        <input v-else-if="field.type === 'date'" 
                               type="date"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               :style="{ fontSize: _bodySmall }"
                        />
                        
                        <!-- Number Input -->
                        <input v-else-if="field.type === 'number'" 
                               type="number"
                               :placeholder="field.placeholder || `Enter ${field.label.toLowerCase()}`"
                               :min="field.validation?.min"
                               :max="field.validation?.max"
                               :step="field.validation?.step || 1"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               :style="{ fontSize: _bodySmall }"
                        />
                        
                        <!-- File Upload -->
                        <div v-else-if="field.type === 'file'" 
                             class="w-full px-6 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer">
                          <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-500 mb-2"></i>
                          <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400">
                            Upload {{ field.label.toLowerCase() }}
                          </p>
                          <p :style="{ fontSize: _bodySmall }" class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            {{ field.attributes?.accept || 'All files' }} • Max {{ field.attributes?.maxSize || '10MB' }}
                          </p>
                        </div>
                        
                        <!-- Checkbox -->
                        <label v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2 cursor-pointer">
                          <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                          <span :style="{ fontSize: _bodySmall }" class="text-gray-700 dark:text-gray-300">
                            {{ field.label }}
                          </span>
                        </label>
                        
                        <!-- Radio buttons -->
                        <div v-else-if="field.type === 'radio'" class="space-y-2">
                          <label v-for="option in field.options" :key="option" class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" :name="field.name" :value="option" class="border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span :style="{ fontSize: _bodySmall }" class="text-gray-700 dark:text-gray-300">
                              {{ option }}
                            </span>
                          </label>
                        </div>
                        
                        <!-- Dynamic Table -->
                        <div v-else-if="field.type === 'dynamic_table'" 
                             class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 p-4">
                          <div class="flex items-center justify-between mb-3">
                            <h6 :style="{ fontSize: _bodySmall }" class="font-medium text-gray-900 dark:text-white">
                              {{ field.label }}
                            </h6>
                            <button class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                              <i class="fas fa-plus mr-1"></i>Add Row
                            </button>
                          </div>
                          <div class="text-sm text-gray-600 dark:text-gray-400">
                            Table with {{ field.attributes?.columns?.length || 0 }} columns
                            <span v-if="field.attributes?.columns" class="ml-2 text-xs">
                              ({{ field.attributes.columns.map(col => col.label).join(', ') }})
                            </span>
                          </div>
                        </div>
                        
                        <!-- Generic field type -->
                        <div v-else class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm italic">
                          {{ field.type }} field ({{ field.label }})
                        </div>
                      </div>
                      
                      <!-- Field Validation Info -->
                      <div v-if="field.validation" class="text-xs text-gray-500 dark:text-gray-400 flex items-start space-x-1">
                        <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                        <span>
                          {{ Object.entries(field.validation).map(([key, value]) => `${key}: ${value}`).join(', ') }}
                        </span>
                      </div>
                      
                      <!-- Field Description/Help Text -->
                      <div v-if="field.description" class="text-xs text-blue-600 dark:text-blue-400">
                        {{ field.description }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-16">
              <i class="fas fa-info-circle text-gray-400 text-3xl mb-4"></i>
              <p :style="{ fontSize: _body }" class="text-gray-500 dark:text-gray-400">
                No current step information available
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30">
        <div class="flex justify-between items-center">
          <div class="flex space-x-3">
            <button :style="{ fontSize: _bodySmall }" class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <i class="fas fa-pause mr-2"></i>
              Pause
            </button>
            <button :style="{ fontSize: _bodySmall }" class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
              <i class="fas fa-stop mr-2"></i>
              Cancel
            </button>
          </div>
          <div class="flex space-x-3">
            <button :style="{ fontSize: _bodySmall }" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <i class="fas fa-arrow-left mr-2"></i>
              Previous
            </button>
            <button :style="{ fontSize: _body }" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <i class="fas fa-arrow-right mr-2"></i>
              Next Step
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
