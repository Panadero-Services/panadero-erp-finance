<!--
  Workflow Entity Selection Component
  @version 2.0.0
  @description Standardized entity selection for workflows
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useColors } from './useColors.js'

// Color system
const { colors } = useColors()

// STANDARDIZED PROPS - Same for all 4 components
const props = defineProps({
  step: {
    type: Object,
    required: true
  },
  stepData: {
    type: Object,
    default: () => ({})
  },
  scaling: {
    type: Object,
    required: true
  },
  workflowStore: {
    type: Object,
    required: true
  },
  workflowId: {
    type: [String, Number],
    required: true
  },
  infoOnly: {
    type: Boolean,
    default: false
  }
})

// Get activeWorkflow from store reactively (SSOT)
const activeWorkflow = computed(() => {
  return props.workflowStore.workflows.find(w => w.id === props.workflowId) || null
})

// State
const selectedEntity = ref(props.stepData.selectedEntity || null)
const searchQuery = ref('')
const isLoading = ref(false)
const availableEntities = ref([])
const apiError = ref(null) // ADD ERROR STATE

// Computed
const apiEndpoint = computed(() => props.step.entity_selection?.api_endpoint || '/api/vendors')
const searchFields = computed(() => props.step.entity_selection?.search_fields || ['company_name'])
const displayField = computed(() => props.step.displayField || 'company_name')

const filteredEntities = computed(() => {
  if (!searchQuery.value) return availableEntities.value
  
  const query = searchQuery.value.toLowerCase()
  return availableEntities.value.filter(entity => {
    return searchFields.value.some(field => {
      const value = entity[field]
      return value && value.toString().toLowerCase().includes(query)
    })
  })
})

const canSubmit = computed(() => {
  return selectedEntity.value && !isLoading.value
})

// Methods
async function fetchEntities() {
  if (!apiEndpoint.value) return
  
  isLoading.value = true
  apiError.value = null // CLEAR PREVIOUS ERRORS
  
  try {
    const response = await fetch(apiEndpoint.value)
    
    if (!response.ok) {
      apiError.value = `API call failed: ${response.status} ${response.statusText}`
      console.error('API call failed:', response.status)
      return
    }
    
    const data = await response.json()
    availableEntities.value = data.entities || data.data || data || []
    
  } catch (error) {
    apiError.value = `Failed to fetch entities: ${error.message}`
    console.error('Failed to fetch entities:', error)
  } finally {
    isLoading.value = false
  }
}

function selectEntity(entity) {
  selectedEntity.value = entity
}

function clearSelection() {
  selectedEntity.value = null
}

// STANDARDIZED SUBMIT METHOD - Same pattern for all 4 components
function submitStep() {
  if (!canSubmit.value) return
  
  // Store the selected entity in step data
  if (selectedEntity.value) {
    // Update the step data with selected entity
    const stepData = {
      selectedEntity: selectedEntity.value,
      completed: true,
      completedAt: new Date().toISOString()
    }
    
    // Store in workflow step data
    if (activeWorkflow.value) {
      const currentStepIndex = activeWorkflow.value.currentStep - 1
      if (activeWorkflow.value.steps[currentStepIndex]) {
        activeWorkflow.value.steps[currentStepIndex].data = {
          ...activeWorkflow.value.steps[currentStepIndex].data,
          ...stepData
        }
      }
    }
  }
  
  // Advance to next step
  props.workflowStore.advanceCurrentStep(props.workflowId)
}

// Lifecycle
onMounted(() => {
  fetchEntities()
})
</script>

<template>
  <div class="space-y-4">
    <!-- STANDARDIZED HEADER - Same for all 4 components
    <div class="flex items-center justify-between">
      <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">
        {{ step.name || 'Entity Selection' }} {{activeWorkflow.currentStep == step.order}}
      </h3>
      <span :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
        Step {{ step.order || '?' }} 
      </span>
    </div> -->

    <div class="text-right text-xxs"> activeWorkflow.id: {{activeWorkflow.id}}</div>
    <div class="text-right text-xxs"> activeWorkflow.currentStep: {{activeWorkflow.currentStep}}</div>

    <!-- INFO ONLY MODE - Same pattern for all 4 components -->
    <div v-if="infoOnly" :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
      <h4 :style="{ fontSize: scaling.font.body }" :class="['font-medium mb-2', colors.primary.text]">
        <i class="fas fa-database text-blue-600 mr-2"></i>
        Entity Selection 
      </h4>
      
      <div v-if="selectedEntity" class="space-y-2">
        <p :style="{ fontSize: scaling.font.body }" :class="colors.secondary.text">
          Selected: <span class="font-medium text-blue-600 dark:text-blue-400">{{ selectedEntity[displayField] || selectedEntity.name }}</span>
        </p>
        <p :style="{ fontSize: scaling.font.caption }" :class="colors.secondary.text">
          ID: {{ selectedEntity.id }}
        </p>
      </div>
      
      <div v-else :style="{ fontSize: scaling.font.caption }" :class="['text-gray-500 dark:text-gray-400']">
        No entity selected
      </div>
    </div>

    <!-- FULL MODE - Same structure for all 4 components -->
    <div v-else class="space-y-6">
      <!-- Description -->
      <p v-if="step.description" :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-300">
        {{ step.description }}
      </p>

      <!-- CRITICAL ERROR SCREEN - Schema fetch failed -->
      <div v-if="!activeWorkflow?.entityDataStructure" class="text-center py-8">
        <div class="text-red-500 dark:text-red-400">
          <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
          <h5 :style="{ fontSize: scaling.font.body }" class="font-medium mb-2">
            Initial Conditions Not Met
          </h5>
          <p :style="{ fontSize: scaling.font.caption }" class="text-gray-600 dark:text-gray-300">
            Schema fetch failed. Cannot proceed with entity selection until this is resolved.
          </p>
          <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
            <p :style="{ fontSize: scaling.font.caption }" class="text-red-700 dark:text-red-300">
              <i class="fas fa-info-circle mr-1"></i>
              Check the Step Information panel for error details.
            </p>
          </div>
        </div>
      </div>

      <!-- NORMAL CONTENT - Only show if schema is available -->
      <div v-else class="space-y-4">
        <!-- Search Input -->
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search entities..."
          :class="['w-full px-3 py-2 border rounded-md', 'border-gray-300 dark:border-gray-600', 'bg-white dark:bg-gray-800', 'text-gray-900 dark:text-white']"
          :style="{ fontSize: scaling.font.body }"
        />

        <!-- API ERROR MESSAGE -->
        <div v-if="apiError" class="text-center py-8">
          <div class="text-red-500 dark:text-red-400">
            <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
            <h5 :style="{ fontSize: scaling.font.body }" class="font-medium mb-2">
              API Error
            </h5>
            <p :style="{ fontSize: scaling.font.caption }" class="text-gray-600 dark:text-gray-300">
              {{ apiError }}
            </p>
            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
              <p :style="{ fontSize: scaling.font.caption }" class="text-red-700 dark:text-red-300">
                <i class="fas fa-info-circle mr-1"></i>
                Check the API endpoint: {{ apiEndpoint }}
              </p>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-else-if="isLoading" class="text-center py-8 text-gray-500 dark:text-gray-400">
          <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
          <p :style="{ fontSize: scaling.font.body }">Loading entities...</p>
        </div>

        <!-- Entity List -->
        <div v-else-if="!apiError" class="grid md:grid-cols-2 xl:grid-cols-3 gap-3 max-h-64 overflow-y-auto">
          <div
            v-for="entity in filteredEntities"
            :key="entity.id"
            @click="selectEntity(entity)"
            :class="[
              'p-3 rounded-lg border cursor-pointer transition-colors',
              selectedEntity?.id === entity.id
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600'
            ]"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <h6 :style="{ fontSize: scaling.font.body }" class="font-medium text-gray-900 dark:text-white">
                  {{ entity[displayField] || entity.name }}
                </h6>
                <p :style="{ fontSize: scaling.font.caption }" class="text-gray-500 dark:text-gray-400">
                  ID: {{ entity.id }}
                </p>
              </div>
              <div v-if="selectedEntity?.id === entity.id" class="text-blue-600">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- No Results - Only show if no API error -->
        <div v-if="!isLoading && !apiError && filteredEntities.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
          <i class="fas fa-search text-2xl mb-2"></i>
          <p :style="{ fontSize: scaling.font.body }">No entities found</p>
        </div>

        <!-- Selected Entity Summary -->
        <div v-if="selectedEntity" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
          <div class="flex items-center justify-between">
            <div>
              <h6 :style="{ fontSize: scaling.font.body }" class="font-medium text-green-800 dark:text-green-200">
                Selected: {{ selectedEntity[displayField] || selectedEntity.name }}
              </h6>
              <p :style="{ fontSize: scaling.font.caption }" class="text-green-600 dark:text-green-300">
                ID: {{ selectedEntity.id }}
              </p>
            </div>
            <button
              @click="clearSelection"
              :style="{ fontSize: scaling.font.caption }"
              class="px-3 py-1 text-sm text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 rounded"
            >
              Clear
            </button>
          </div>
        </div>

        <!-- STANDARDIZED SUBMIT BUTTON - Only show if schema is available -->
        <div v-if="selectedEntity" class="flex justify-end">
          <button
            @click="submitStep"
            :disabled="!canSubmit"
            :class="[
              'px-6 py-2 rounded-lg font-medium transition-colors',
              canSubmit
                ? 'bg-blue-600 hover:bg-blue-700 text-white'
                : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
            ]"
            :style="{ fontSize: scaling.font.body }"
          >
            <i class="fas fa-check mr-2"></i>
            Confirm Selection
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
