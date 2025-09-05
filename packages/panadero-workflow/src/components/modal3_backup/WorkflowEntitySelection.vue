<!--
  Workflow Entity Selection Component
  @version 1.4.0
  @description Handles entity selection step types in workflows - step type specific
  @cleanup Complete rewrite to fix template structure and follow development rules
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useColors } from './useColors.js'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Color system
const { colors } = useColors()

// Props
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
  infoOnly: {
    type: Boolean,
    default: false
  },
  activeWorkflow: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['update-step-data'])

// Composables
//const settings = useWorkflowSettings()

// State
const selectedEntity = ref(props.stepData.selectedEntity || null)
const searchQuery = ref('')
const isSubmitting = ref(false)
const isLoading = ref(false)

// API integration
const availableEntities = ref([])
const apiEndpoint = computed(() => props.step.entity_selection?.api_endpoint || '/api/vendors')
const searchFields = computed(() => props.step.entity_selection?.search_fields || ['company_name'])

// Dynamic font sizes - ONLY changing styling references
const _value = computed(() => props.scaling.font.subtitle) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _bodySmall = computed(() => props.scaling.font.small) // WAS: `${settings.fontSizesComputed.value.bodySmall}px`

// Lifecycle
onMounted(() => {
  fetchEntities()
})

// Check initial conditions - schema must be available for entity selection
const hasInitialConditionsMet = computed(() => {
  // Check if schema fetch failed - this is an initial condition
  if (!props.activeWorkflow?.entityDataStructure) {
    return false // Schema fetch failed, initial conditions not met
  }
  return true // Schema available, initial conditions met
})

// Computed
const filteredEntities = computed(() => {
  if (!searchQuery.value) return availableEntities.value
  
  const query = searchQuery.value.toLowerCase()
  return availableEntities.value.filter(entity => {
    // Use the configured search fields from the workflow step
    return searchFields.value.some(field => {
      const value = entity[field]
      return value && value.toString().toLowerCase().includes(query)
    })
  })
})

const canSubmit = computed(() => {
  return selectedEntity.value && !isSubmitting.value
})

// Methods
async function fetchEntities() {
  isLoading.value = true
  
  try {
    const response = await fetch(apiEndpoint.value)
    
    if (!response.ok) {
      console.error('API call failed:', response.status)
      return
    }
    
    const data = await response.json()
    availableEntities.value = data.entities || data.data || data || []
    
  } catch (error) {
    console.error('Failed to fetch entities:', error)
  } finally {
    isLoading.value = false
  }
}

function selectEntity(entity) {
  // Check initial conditions before allowing selection
  if (!hasInitialConditionsMet.value) {
    console.warn('Cannot select entity: Initial conditions not met (schema fetch failed)')
    return
  }

  selectedEntity.value = entity
  emit('update-step-data', props.step.id, { selectedEntity: entity })
  
  console.debug('🟢 Emitted update-step-data event')
}

function clearSelection() {
  selectedEntity.value = null
  emit('update-step-data', props.step.id, { selectedEntity: null })
}

async function submitSelection() {
  console.debug('🚀 submitSelection called')
  if (!canSubmit.value) {
    console.debug('🚀 Cannot submit - conditions not met')
    return
  }
  
  isSubmitting.value = true
  
  try {
    console.debug('🚀 Emitting update-step-data event')
    // Update step data
    emit('update-step-data', props.step.id, {
      selectedEntity: selectedEntity.value,
      completed: true,
      completedAt: new Date().toISOString()
    })
    
    console.debug('✅ Entity selection completed')
    
  } catch (error) {
    console.error('Failed to submit entity selection:', error)
  } finally {
    isSubmitting.value = false
  }
}

// Debug
console.debug('WorkflowEntitySelection props:', props)
console.debug('WorkflowEntitySelection selectedEntity:', selectedEntity.value)
console.debug('WorkflowEntitySelection hasInitialConditionsMet:', hasInitialConditionsMet.value)

</script>

<template>
  <div class="space-y-4">

    <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mt-2">
      Step {{ props.step.order || '?' }} -  Input section
    </h3>
    
    <!-- Info Only Mode - For Column 2 Display -->
    <div v-if="infoOnly" class="space-y-4">
      <div :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
        <div class="flex justify-between items-start mb-3">
          <h4 :style="{ fontSize: _value }" :class="['font-semibold', colors.primary.text]">
            <i :class="[props.step.icon || 'fas fa-database', props.step.iconColor || 'text-blue-600', 'mr-2']"></i>
            {{ props.step.title || 'Entity Selection' }}
          </h4>
        </div>
        
        <div class="space-y-3">
          <div v-if="props.step.description" :class="colors.secondary.text">
            <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
          </div>
          
          <div v-if="props.step.entityType" :class="colors.secondary.text">
            <p :style="{ fontSize: _bodySmall }">
              <i class="fas fa-info-circle mr-1"></i>
              Select a {{ props.step.entityType }} for this workflow
            </p>
          </div>
        </div>
        <div v-if="selectedEntity" class="">
          <span :class="colors.secondary.text">Selected: </span>
          <span class="font-medium text-blue-600 dark:text-blue-400">{{ selectedEntity.name || selectedEntity.id }}</span>
        </div>
        
        <!-- Current Step Data Only -->
        <div v-if="selectedEntity" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
          <div :style="{ fontSize: scaling.font.small }" :class="['font-medium mb-2', colors.primary.textMuted]">
            <i class="fas fa-database mr-1 text-blue-600"></i>
            Step Data
          </div>
          <div :class="['bg-gray-100 dark:bg-gray-700 rounded p-2 font-mono text-xs overflow-x-auto', colors.secondary.text]">
            selectedEntity: {{ JSON.stringify(selectedEntity, null, 2) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Full Component Mode - For Column 3 Display -->
    <div v-else class="space-y-6">
    <!-- Entity Selection Information -->
    <div :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
      <div class="flex justify-between items-start mb-3">
        <h4 :style="{ fontSize: _value }" :class="['font-semibold', colors.primary.text]">
          <i :class="[props.step.icon || 'fas fa-database', props.step.iconColor || 'text-blue-600', 'mr-2']"></i>
          {{ props.step.title || 'Entity Selection Required' }}
        </h4>

      </div>
      
      <div class="space-y-3">
        <div v-if="props.step.description" :class="colors.secondary.text">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
        
        <div v-if="props.step.entityType" :class="colors.secondary.text">
          <p :style="{ fontSize: _bodySmall }">
            <i class="fas fa-info-circle mr-1"></i>
            Select a {{ props.step.entityType }} for this workflow
          </p>
        </div>
      </div>
    </div>

    <!-- Search and Selection -->
    <div :class="['rounded-lg border p-4', colors.primary.bg, colors.secondary.border]">
      <h5 :style="{ fontSize: _body }" :class="['font-medium mb-3', colors.primary.text]">
        <i :class="[props.step.searchIcon || 'fas fa-search', props.step.searchIconColor || 'text-blue-600', 'mr-2']"></i>
        {{ props.step.searchTitle || 'Available Entities' }}
      </h5>
      
      <!-- Initial Conditions Check -->
      <div v-if="!hasInitialConditionsMet" class="text-center py-8">
        <div class="text-red-500 dark:text-red-400">
          <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
          <h5 :style="{ fontSize: _body }" class="font-medium mb-2">
            Initial Conditions Not Met
          </h5>
          <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-300">
            Schema fetch failed. Cannot proceed with entity selection until this is resolved.
          </p>
          <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
            <p :style="{ fontSize: _bodySmall }" class="text-red-700 dark:text-red-300">
              <i class="fas fa-info-circle mr-1"></i>
              Check the Step Information panel for error details.
            </p>
          </div>
        </div>
      </div>
      
      <!-- Search Input - Only show if initial conditions are met -->
      <div v-else class="mb-4">
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="props.step.searchPlaceholder || 'Search entities...'"
          :class="['w-full px-3 py-2 border rounded-md text-sm transition-colors', colors.form.border, colors.form.focus, colors.form.input, colors.primary.text]"
          :style="{ fontSize: _body }"
        />
      </div>
      
      <!-- Loading State -->
      <div v-if="isLoading" :class="['text-center py-8', colors.secondary.text]">
        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
        <p :style="{ fontSize: _bodySmall }">Loading entities...</p>
      </div>
      
      <!-- Entity List -->
      <div v-else-if="hasInitialConditionsMet" class="max-h-64 overflow-y-auto grid md:grid-cols-2 xl:grid-cols-3 gap-3">
        <div v-for="entity in filteredEntities" :key="entity.id"
             @click="selectEntity(entity)"
             :class="[
               'p-3 rounded-lg border transition-colors',
               !hasInitialConditionsMet ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
               selectedEntity?.id === entity.id
                 ? 'border-purple-500 bg-purple-100 dark:bg-purple-900/20'
                 : 'border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-600 dark:bg-gray-900'
             ]">
          
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h6 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white">
                {{ entity[props.step.displayField || 'company_name'] || entity.name }}
              </h6>
              <div class="flex items-center space-x-3 mt-1">
                <span v-if="props.step.subtitleField1" :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
                  <i :class="[props.step.subtitleIcon1 || 'fas fa-tag', 'mr-1']"></i>
                  {{ entity[props.step.subtitleField1] }}
                </span>
                <span v-if="props.step.subtitleField2" :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
                  <i :class="[props.step.subtitleIcon2 || 'fas fa-user', 'mr-1']"></i>
                  {{ entity[props.step.subtitleField2] }}
                </span>
              </div>
            </div>
            
            <div v-if="selectedEntity?.id === entity.id" class="text-purple-600">
              <i class="fas fa-check-circle text-xl"></i>
            </div>
          </div>
        </div>
      </div>
      
      <!-- No Results -->
      <div v-if="!isLoading && hasInitialConditionsMet && filteredEntities.length === 0" :class="['text-center py-8', colors.secondary.text]">
        <i class="fas fa-search text-2xl mb-2"></i>
        <p :style="{ fontSize: _bodySmall }">No entities found</p>
        <p v-if="searchQuery" :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
          Try adjusting your search terms
        </p>
      </div>
    </div>

    <!-- Selected Entity Summary -->
    <div v-if="selectedEntity" :class="['rounded-lg border p-4', colors.primary.bg, colors.secondary.border]">
      <h5 :style="{ fontSize: _body }" :class="['font-medium mb-3', colors.primary.text]">
        <i class="fas fa-check-circle text-green-600 mr-2"></i>
        Selected Entity
      </h5>
      
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <h6 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white">
            {{ selectedEntity[props.step.displayField || 'company_name'] || selectedEntity.name }}
          </h6>
          <div class="flex items-center space-x-3 mt-1">
            <span v-if="props.step.subtitleField1" :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
              <i :class="[props.step.subtitleIcon1 || 'fas fa-tag', 'mr-1']"></i>
              {{ selectedEntity[props.step.subtitleField1] }}
            </span>
            <span v-if="props.step.subtitleField2" :style="{ fontSize: _bodySmall }" class="text-gray-500 dark:text-gray-400">
              <i :class="[props.step.subtitleIcon2 || 'fas fa-user', 'mr-1']"></i>
              {{ selectedEntity[props.step.subtitleField2] }}
            </span>
          </div>
        </div>
        
        <button
          @click="clearSelection"
          :class="['px-3 py-1 text-sm rounded-md transition-colors', colors.form.border, colors.form.input, colors.primary.text, 'hover:bg-gray-100 dark:hover:bg-gray-700']"
          :style="{ fontSize: _bodySmall }"
        >
          <i class="fas fa-times mr-1"></i>
          Clear
        </button>
      </div>
    </div>

    <!-- Submit Button -->
    <div v-if="selectedEntity && hasInitialConditionsMet" class="flex justify-end">
      <button
        @click="submitSelection"
        :disabled="!canSubmit"
        :class="[
          'px-6 py-2 rounded-lg font-medium transition-colors',
          canSubmit
            ? 'bg-blue-600 hover:bg-blue-700 text-white'
            : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
        ]"
        :style="{ fontSize: _body }"
      >
        <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-check mr-2"></i>
        {{ isSubmitting ? 'Submitting...' : 'Confirm Selection' }}
      </button>
    </div>
    </div>
  </div>
</template>
