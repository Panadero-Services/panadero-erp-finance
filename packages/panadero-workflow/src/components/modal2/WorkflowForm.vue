<!--
  Workflow Form Component
  @version 1.2.1
  @description Handles form submission step types in workflows - step type specific
  @fixes Overflow and scrolling issues resolved in Modal.vue
  @cleanup Removed debug visual elements for production use
  @cleanup Removed entity_selector code - separation of concerns
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useColors } from './useColors.js'
//import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Color system
const { colors } = useColors()

// Props
const props = defineProps({
  step: { type: Object, required: true },
  stepData: { type: Object, default: () => ({}) },
  scaling: { type: Object, required: true },
  infoOnly: { type: Boolean, default: false },
  workflowStore: { type: Object, required: true },
  workflowId: { type: [String, Number], required: true },
  activeWorkflow: { type: Object, default: null }
})

// Emits
const emit = defineEmits(['update-step-data'])

// State
const formData = ref({ ...props.stepData.formData || {} })
const isSubmitting = ref(false)

// Dynamic font sizes
const _value = computed(() => props.scaling.font.subtitle)
const _body = computed(() => props.scaling.font.body)
const _bodySmall = computed(() => props.scaling.font.small)
const _caption = computed(() => props.scaling.font.caption)

// 1. FIX REACTIVITY: Proper store update
function updateField(fieldName, value) {
  formData.value[fieldName] = value
  
  // CRITICAL FIX: Update the store immediately for Column 2 reactivity
  if (props.activeWorkflow?.steps) {
    const currentStepIndex = props.activeWorkflow.currentStep - 1
    if (props.activeWorkflow.steps[currentStepIndex]) {
      // Ensure the data object exists
      if (!props.activeWorkflow.steps[currentStepIndex].data) {
        props.activeWorkflow.steps[currentStepIndex].data = {}
      }
      // Update formData in the store
      props.activeWorkflow.steps[currentStepIndex].data.formData = { ...formData.value }
    }
  }
  
  // Also emit for compatibility
  emit('update-step-data', props.step.id, { formData: formData.value })
}

async function submitStep() {
  if (!canSubmit.value) return
  
  isSubmitting.value = true
  
  try {
    // Validate required fields
    const requiredFields = getRequiredFields()
    const missingFields = requiredFields.filter(field => !formData.value[field.name])
    
    if (missingFields.length > 0) {
      throw new Error(`Missing required fields: ${missingFields.map(f => f.label).join(', ')}`)
    }
    
    // Emit completion
    emit('update-step-data', props.step.id, {
      formData: formData.value,
      completed: true,
      completedAt: new Date().toISOString()
    })
    
    // Advance to next step using SSOT
    props.workflowStore.advanceCurrentStep(props.workflowId)
    
  } catch (error) {
    console.error('Form submission failed:', error)
  } finally {
    isSubmitting.value = false
  }
}

// Computed - USE JSON CONFIG SYSTEM
const formFields = computed(() => {
  if (!props.step.form_schema?.sections) return []
  
  const fields = []
  props.step.form_schema.sections.forEach(section => {
    section.fields.forEach(field => {
      fields.push(field)
    })
  })
  return fields
})

const completedFields = computed(() => {
  return formFields.value.filter(field => {
    const value = formData.value[field.name]
    return value !== null && value !== undefined && value !== ''
  }).length
})

const progressPercentage = computed(() => {
  if (formFields.value.length === 0) return 0
  return Math.round((completedFields.value / formFields.value.length) * 100)
})

const canSubmit = computed(() => {
  if (!props.step.form_schema) return false
  
  const requiredFields = getRequiredFields()
  return requiredFields.every(field => {
    const value = formData.value[field.name]
    return value !== null && value !== undefined && value !== ''
  }) && !isSubmitting.value
})

function getRequiredFields() {
  if (!props.step.form_schema?.sections) return []
  
  const requiredFields = []
  props.step.form_schema.sections.forEach(section => {
    section.fields.forEach(field => {
      if (field.required) {
        requiredFields.push(field)
      }
    })
  })
  return requiredFields
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

  <!-- Header in line with other step types 
  <h3 :style="{ fontSize: scaling.font.body }" class="font-semibold text-gray-900 dark:text-white mt-2">
    Step {{ props.step.order || '?' }} - Input section
  </h3>-->
  
  <!-- INFO ONLY MODE - Column 2 (no progress bar here) -->
  <div v-if="infoOnly" :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
    <h4 :style="{ fontSize: scaling.font.body }" :class="['font-medium mb-2', colors.primary.text]">
      <i class="fas fa-edit text-green-600 mr-2"></i>
      Form Submission
    </h4>

    <div v-if="formFields.length > 0" :class="['text-sm', colors.secondary.text]">
      {{ formFields.length }} fields to complete
    </div>
    
    <!-- Current Step Data Only -->
    <div v-if="Object.keys(formData).length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
      <div :style="{ fontSize: scaling.font.small }" :class="['font-medium mb-2', colors.primary.textMuted]">
        <i class="fas fa-database mr-1 text-blue-600"></i>
        Step Data
      </div>
      <div :class="['bg-gray-100 dark:bg-gray-700 rounded p-2 font-mono text-xs overflow-x-auto', colors.secondary.text]">
        formData: {{ JSON.stringify(formData, null, 2) }}
      </div>
    </div>
  </div>
  
  <!-- FULL INTERACTIVE MODE - Column 3 -->
  <div v-else class="space-y-6">



    <!-- Form Information -->
    <div :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
      <div class="flex justify-between items-start mb-3">
        <h4 :style="{ fontSize: _value }" :class="['font-semibold', colors.primary.text]">
          <i class="fas fa-edit text-green-600 mr-2"></i>
          Form Submission Required
        </h4>


    <!-- 2. MOVE PROGRESS BAR BACK TO COLUMN 3 -->
    <div class="">
      <div class="flex justify-between items-center mb-1">
        <span :style="{ fontSize: _bodySmall }" class="font-medium text-blue-800 dark:text-blue-200">
        </span>
        <span :style="{ fontSize: _bodySmall }" class="text-blue-600 dark:text-blue-300">
          {{ completedFields }}/{{ formFields.length }} 
        </span>
        <span :style="{ fontSize: _bodySmall }" class="text-blue-600 dark:text-blue-300 ml-16">completed 
        </span>
      </div>
      <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-1.5">
        <div 
          class="bg-blue-600 dark:bg-blue-400 h-1.5 rounded-full transition-all duration-300"
          :style="{ width: `${progressPercentage}%` }"
        ></div>
      </div>
    </div>




      </div>




      
      <div class="space-y-3">
        <div v-if="props.step.description" :class="colors.secondary.text">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
      </div>
    </div>






    <!-- Form Fields from JSON Config -->
    <div v-if="props.step.form_schema?.sections" class="space-y-6">
      
      <div v-for="section in props.step.form_schema.sections" :key="section.title" 
           :class="['rounded-lg border p-4', colors.primary.bg, colors.secondary.border]">
        
        <h5 :style="{ fontSize: _body }" :class="['font-medium border-b pb-2 mb-4', colors.primary.text, colors.secondary.border]">
          {{ section.title }}
        </h5>
        
        <!-- Grid 2-column layout for fields -->
        <div class="grid md:grid-cols-2 gap-4">
          <div v-for="field in section.fields" :key="field.name" class="space-y-1">
            
            <!-- Field Label -->
            <label :style="{ fontSize: _bodySmall }" :class="['block font-medium', colors.primary.textMuted]">
              {{ field.label }}
              <span v-if="field.required" class="text-red-500 ml-1">*</span>
            </label>
            
            <!-- Text Input -->
            <input 
              v-if="field.type === 'text' || field.type === 'number'"
              :type="field.type"
              :value="formData[field.name] || ''"
              @input="updateField(field.name, $event.target.value)"
              :placeholder="field.placeholder || ''"
              :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
              :style="{ fontSize: _body }"
            />
            
            <!-- ADD: Date Input -->
            <input 
              v-else-if="field.type === 'date'"
              type="date"
              :value="formData[field.name] || ''"
              @input="updateField(field.name, $event.target.value)"
              :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
              :style="{ fontSize: _body }"
            />

            <!-- ADD: DateTime-Local Input -->
            <input 
              v-else-if="field.type === 'datetime-local'"
              type="datetime-local"
              :value="formData[field.name] || ''"
              @input="updateField(field.name, $event.target.value)"
              :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
              :style="{ fontSize: _body }"
            />

            <!-- ADD: Month Input -->
            <input 
              v-else-if="field.type === 'month'"
              type="month"
              :value="formData[field.name] || ''"
              @input="updateField(field.name, $event.target.value)"
              :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
              :style="{ fontSize: _body }"
            />
            
            <!-- Select Dropdown -->
            <select 
              v-else-if="field.type === 'select'"
              :value="formData[field.name] || ''"
              @change="updateField(field.name, $event.target.value)"
              :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
              :style="{ fontSize: _body }"
            >
              <option value="">Select {{ field.label.toLowerCase() }}</option>
              <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
            </select>
            
            <!-- Checkbox -->
            <label v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2 cursor-pointer">
              <input type="checkbox" 
                     :checked="formData[field.name] || false"
                     class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                     @change="updateField(field.name, $event.target.checked)">
              <span :style="{ fontSize: _body }" :class="colors.primary.textMuted">
                {{ field.label }}
              </span>
            </label>
            
            <!-- Textarea -->
            <textarea 
              v-else-if="field.type === 'textarea'"
              :value="formData[field.name] || ''"
              @input="updateField(field.name, $event.target.value)"
              :placeholder="field.placeholder || ''"
              rows="3"
              :class="['block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none', colors.form.border, colors.form.focus, colors.form.input]"
              :style="{ fontSize: _body }"
            ></textarea>

          </div>
        </div>
      </div>
    </div>
    
    <!-- No Form Schema -->
    <div v-else :class="['rounded-lg border p-8 text-center', colors.primary.bg, colors.secondary.border]">
      <i class="fas fa-exclamation-triangle text-gray-400 text-3xl mb-4"></i>
      <p :style="{ fontSize: _body }" :class="colors.secondary.text">
        No form schema defined for this step
      </p>
      <p :style="{ fontSize: _caption }" :class="[colors.secondary.text, 'mt-1']">
        Please configure the form schema in the workflow definition
      </p>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
      <button
        @click="submitStep"
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
        {{ isSubmitting ? 'Confirming...' : 'Confirm Form' }}
      </button>
    </div>
  </div>
</div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
