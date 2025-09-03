<!--
  Workflow Entity Selection Component
  @version 1.4.0
  @description Handles entity selection step types in workflows - step type specific
  @cleanup Complete rewrite to fix template structure and follow development rules
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

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
  }
})

// Emits
const emit = defineEmits(['update-step-data', 'step-completed'])

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
  return selectedEntity.value !== null && !isSubmitting.value
})

const entityConfig = computed(() => {
  return props.step.sharedEntity || {
    identifierField: 'id',
    displayField: 'company_name',
    descriptionField: 'business_type'
  }
})

// Methods
async function fetchEntities() {
  if (!apiEndpoint.value) return
  
  isLoading.value = true
  try {
    // API call to fetch vendors
    const response = await fetch(apiEndpoint.value)
    if (response.ok) {
      const data = await response.json()
      availableEntities.value = data
    } else {
      console.error('API call failed:', response.status)
      availableEntities.value = []
    }
  } catch (error) {
    console.error('Failed to fetch entities:', error)
    availableEntities.value = []
  } finally {
    isLoading.value = false
  }
}

function selectEntity(entity) {
  console.debug('🟢 WORKFLOW ENTITY SELECTION: selectEntity called')
  console.debug('🟢 entity:', entity)
  console.debug('🟢 props.step.id:', props.step.id)
  
  selectedEntity.value = entity
  emit('update-step-data', props.step.id, { selectedEntity: entity })
  
  console.debug('🟢 Emitted update-step-data event')
}

function clearSelection() {
  selectedEntity.value = null
  emit('update-step-data', props.step.id, { selectedEntity: null })
}

async function submitSelection() {
  if (!canSubmit.value) return
  
  isSubmitting.value = true
  
  try {
    // Emit completion
    emit('step-completed', {
      stepId: props.step.id,
      selectedEntity: selectedEntity.value,
      completedAt: new Date().toISOString()
    })
    
    // Update step data
    emit('update-step-data', props.step.id, {
      selectedEntity: selectedEntity.value,
      completed: true,
      completedAt: new Date().toISOString()
    })
    
  } catch (error) {
    console.error('Failed to submit entity selection:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="space-y-4">

    <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mt-2">
      Step {{ props.step.order || '?' }} -  Input section
    </h3>

    <!-- Info Only Mode - For Column 2 Display -->
    <div v-if="infoOnly" class="bg-white dark:bg-gray-800 rounded-lg p-3">
      <h4 :style="{ fontSize: scaling.font.body }" class="font-medium text-gray-700 dark:text-gray-300 mb-2">
        <i class="fas fa-database text-blue-600 mr-1"></i>
        Entity Selection
      </h4>
      <div class="space-y-2">
        <div v-if="props.step.entity_type" class="text-gray-600 dark:text-gray-400">
          Select from: {{ props.step.entity_type }}
        </div>
        <div v-if="selectedEntity" class="">
          <span class="text-gray-500 dark:text-gray-400">Selected: </span>
          <span class="font-medium text-blue-600 dark:text-blue-400">{{ selectedEntity.name || selectedEntity.id }}</span>
        </div>
      </div>
    </div>

    <!-- Full Component Mode - For Column 3 Display -->
    <div v-else class="space-y-6">
    <!-- Entity Selection Information -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <div class="flex justify-between items-start mb-3">
        <h4 :style="{ fontSize: _value }" class="font-semibold text-gray-900 dark:text-white">
          <i :class="[props.step.icon || 'fas fa-database', props.step.iconColor || 'text-blue-600', 'mr-2']"></i>
          {{ props.step.title || 'Entity Selection Required' }}
        </h4>

      </div>
      
      <div class="space-y-3">
        <div v-if="props.step.description" class="text-gray-600 dark:text-gray-400">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
        
        <div v-if="props.step.entityType" class="text-gray-600 dark:text-gray-400">
          <p :style="{ fontSize: _bodySmall }">
            <i class="fas fa-info-circle mr-1"></i>
            Select a {{ props.step.entityType }} for this workflow
          </p>
        </div>
      </div>
    </div>

    <!-- Search and Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
        <i :class="[props.step.searchIcon || 'fas fa-search', props.step.searchIconColor || 'text-blue-600', 'mr-2']"></i>
        {{ props.step.searchTitle || 'Available Entities' }}
      </h5>
      
      <!-- Search Input -->
      <div class="mb-4">
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="props.step.searchPlaceholder || 'Search entities...'"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
          :style="{ fontSize: _body }"
        />
      </div>
      
      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-8 text-gray-500 dark:text-gray-400">
        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
        <p :style="{ fontSize: _bodySmall }">Loading entities...</p>
      </div>
      
      <!-- Entity List -->
      <div v-else class="max-h-64 overflow-y-auto grid md:grid-cols-2 xl:grid-cols-3 gap-3">
        <div v-for="entity in filteredEntities" :key="entity.id"
             @click="selectEntity(entity)"
             :class="[
               'p-3 rounded-lg border cursor-pointer transition-colors',
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
        
        <div v-if="filteredEntities.length === 0 && !isLoading" class="text-center py-8 text-gray-500 dark:text-gray-400">
          <i class="fas fa-search text-2xl mb-2"></i>
          <p :style="{ fontSize: _body }">No entities found matching your search</p>
        </div>
      </div>
    </div>

    <!-- Selected Entity Summary -->
    <div v-if="selectedEntity" class="bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
      <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white mb-3">
        <i class="fas fa-check-circle text-green-600 mr-2"></i>
        Selected Entity
      </h5>
      
      <div class="border border-purple-500 bg-purple-100 dark:bg-purple-900/20 rounded-lg p-3">
        <div class="flex items-center justify-between">
          <div>
            <h6 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white">
              {{ selectedEntity.company_name || selectedEntity.name }}
            </h6>
            <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400">
              {{ selectedEntity.business_type || selectedEntity.type }} • {{ selectedEntity.primary_contact_name }}
            </p>
          </div>
          
          <button
            @click="clearSelection"
            class="text-red-500 hover:text-red-700 transition-colors"
            title="Clear selection"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
      <button
        @click="submitSelection"
        :disabled="!canSubmit"
        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
