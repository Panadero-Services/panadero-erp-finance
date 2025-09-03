<!--
  Workflow Checklist Component
  @version 1.1.0
  @description Handles checklist step types in workflows - step type specific
-->
<script setup>
import { ref, computed } from 'vue'
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
const emit = defineEmits(['update-step-data', 'step-completed'])

// Composables
//const settings = useWorkflowSettings()

// Dynamic font sizes - ONLY changing styling references
const _value = computed(() => props.scaling.font.subtitle) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _bodySmall = computed(() => props.scaling.font.small) // WAS: `${settings.fontSizesComputed.value.bodySmall}px`

// Extract only checkbox fields from template
const checkboxFields = computed(() => {
  const fields = []
  
  if (props.step?.form_schema?.sections) {
    props.step.form_schema.sections.forEach(section => {
      section.fields.forEach(field => {
        if (field.type === 'checkbox') {
          fields.push(field)
        }
      })
    })
  }
  
  return fields
})

// State
const comments = ref(props.stepData.comments || '')
const isSubmitting = ref(false)

// Computed
const progress = computed(() => {
  if (checkboxFields.value.length === 0) return 0
  const completed = checkboxFields.value.filter(field => props.stepData[field.name]).length
  return Math.round((completed / checkboxFields.value.length) * 100)
})

const canSubmit = computed(() => {
  const requiredCompleted = checkboxFields.value
    .filter(field => field.required)
    .every(field => props.stepData[field.name])
  return requiredCompleted && !isSubmitting.value
})

const progressClass = computed(() => {
  if (progress.value === 100) return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
  if (progress.value > 50) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
  return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
})

const checkedItems = computed(() => {
  return checkboxFields.value.filter(field => props.stepData[field.name]).length
})

// Methods
function updateField(fieldName, value) {
  emit('update-step-data', props.step.id, { 
    ...props.stepData,
    [fieldName]: value,
    comments: comments.value
  })
}

function submitChecklist() {
  if (!canSubmit.value) return
  
  isSubmitting.value = true
  
  emit('update-step-data', props.step.id, {
    ...props.stepData,
    comments: comments.value,
    completed: true
  })
  
  emit('step-completed')
  
  setTimeout(() => {
    isSubmitting.value = false
  }, 1000)
}

// Get all collected data from all steps
function getAllCollectedData() {
  if (!props.activeWorkflow?.steps) return null
  
  const allData = {}
  
  // Collect data from all steps
  props.activeWorkflow.steps.forEach((step, index) => {
    if (step.data) {
      Object.assign(allData, step.data)
    }
  })
  
  // Add entity data structure fields (empty if not filled)
  if (props.activeWorkflow.entityDataStructure?.fields) {
    props.activeWorkflow.entityDataStructure.fields.forEach(field => {
      if (!(field.name in allData)) {
        allData[field.name] = null // Show empty fields
      }
    })
  }
  
  return Object.keys(allData).length > 0 ? allData : null
}
</script>

<template>
  <div class="space-y-4">

    <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mt-2">
      Step {{ props.step.order || '?' }} -  Input section
    </h3>
        
    <!-- Info Only Mode - For Column 2 Display -->
    <div v-if="infoOnly" :class="['rounded-lg p-3', colors.primary.bg]">
      <h4 :style="{ fontSize: scaling.font.caption }" :class="['font-medium mb-2', colors.primary.textMuted]">
        <i class="fas fa-list-check text-orange-600 mr-1"></i>
        Checklist Items
      </h4>
      <div class="space-y-2">
        <div v-if="checkboxFields.length > 0" :class="['text-sm', colors.secondary.text]">
          {{ checkboxFields.length }} items to complete
        </div>
        <div v-if="progress > 0" class="text-sm">
          <span :class="colors.secondary.text">Progress: </span>
          <span class="font-medium text-blue-600 dark:text-blue-400">{{ progress }}%</span>
        </div>
        <div class="text-sm">
          <span :class="colors.secondary.text">Items checked: </span>
          <span class="font-medium text-blue-600 dark:text-blue-400">{{ checkedItems }} of {{ checkboxFields.length }} items checked</span>
        </div>
        
        <!-- SIMPLE PROGRESS BAR -->
        <div class="mt-3">
          <div class="w-full bg-gray-300 rounded-full h-4">
            <div class="bg-green-500 h-4 rounded-full" style="width: 60%"></div>
          </div>
          <div :class="['text-xs mt-1', colors.secondary.text]">60% Complete</div>
        </div>
        
        <!-- Current Step Data Only -->
        <div v-if="Object.keys(props.stepData).length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
          <div :style="{ fontSize: scaling.font.small }" :class="['font-medium mb-2', colors.primary.textMuted]">
            <i class="fas fa-database mr-1 text-blue-600"></i>
            Step Data
          </div>
          <div :class="['bg-gray-100 dark:bg-gray-700 rounded p-2 font-mono text-xs overflow-x-auto', colors.secondary.text]">
            {{ JSON.stringify(props.stepData, null, 2) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Full Component Mode - For Column 3 Display -->
    <div v-else class="space-y-6">
    
    <!-- Checklist Information -->
    <div :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
      <div class="flex justify-between items-start mb-3">
        <h4 :style="{ fontSize: _value }" :class="['font-semibold', colors.primary.text]">
          <i class="fas fa-list-check text-orange-600 mr-2"></i>
          Checklist Required
        </h4>
      </div>
      
      <div class="space-y-3">
        <div v-if="props.step.description" :class="colors.secondary.text">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
      </div>
    </div>

    <!-- Checklist Items (only checkboxes from template) -->
    <div class="space-y-4">
              <h4 :style="{ fontSize: _bodySmall }" :class="['font-medium border-b pb-2', colors.primary.textMuted, colors.secondary.border]">
        Checklist Items
      </h4>
      
      <div class=" grid lg:grid-cols-2 gap-3">
        <div 
          v-for="field in checkboxFields" 
          :key="field.name" 
          :class="['flex items-start space-x-3 p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors', colors.secondary.border, colors.primary.bg]"
        >
          <input 
            :id="field.name"
            type="checkbox"
            :checked="stepData[field.name] || false"
            @change="updateField(field.name, $event.target.checked)"
            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label 
            :for="field.name"
            :style="{ fontSize: _body }" 
            :class="['flex-1 font-medium cursor-pointer', colors.primary.textMuted]"
          >
            {{ field.label }}
            <span v-if="field.required" class="text-red-500 ml-1">*</span>
          </label>
          <span 
            v-if="stepData[field.name]" 
            class="text-green-600 dark:text-green-400"
          >
            <i class="fas fa-check-circle"></i>
          </span>
        </div>
      </div>
    </div>

    <!-- Optional Comments -->
    <div class="space-y-2">
      <label :style="{ fontSize: _bodySmall }" :class="['block font-medium', colors.primary.textMuted]">
        Comments (Optional)
      </label>
      <textarea 
        :value="comments"
        @input="comments = $event.target.value"
        rows="3"
        placeholder="Add any additional notes or comments..."
        :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
        :style="{ fontSize: _body }"
      ></textarea>
    </div>

    <!-- Submit Button -->
    <button 
      @click="submitChecklist"
      :disabled="!canSubmit"
      :class="[
        'w-full px-4 py-3 rounded-lg font-medium transition-colors',
        canSubmit 
          ? [colors.form.button, 'text-white'] 
          : 'bg-gray-300 text-gray-500 cursor-not-allowed'
      ]"
      :style="{ fontSize: _bodySmall }"
    >
      {{ isSubmitting ? 'Submitting...' : 'Complete Checklist' }}
    </button>
  </div>
</div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
