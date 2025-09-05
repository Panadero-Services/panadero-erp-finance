<!--
  Workflow Form Component
  @version 1.2.1
  @description Handles form submission step types in workflows - step type specific
  @fixes Overflow and scrolling issues resolved in Modal.vue
  @cleanup Removed debug visual elements for production use
  @cleanup Removed entity_selector code - separation of concerns
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
  readOnlyPreview: {
    type: Boolean,
    default: false
  },
  workflowStore: {
    type: Object,
    required: true
  },
  workflowId: {
    type: [String, Number],
    required: true
  }
})

// Get activeWorkflow from store reactively (SSOT)
const activeWorkflow = computed(() => {
  return props.workflowStore.workflows.find(w => w.id === props.workflowId) || null
})


// Emits
const emit = defineEmits(['update-step-data'])

// Composables
//const settings = useWorkflowSettings()

// State
const isSubmitting = ref(false)
const formData = ref(props.stepData.formData || {})

// Dynamic font sizes - ONLY changing styling references
const _value = computed(() => props.scaling.font.subtitle) // WAS: `${settings.fontSizesComputed.value.h4}px`
const _body = computed(() => props.scaling.font.body) // WAS: `${settings.fontSizesComputed.value.body}px`
const _bodySmall = computed(() => props.scaling.font.small) // WAS: `${settings.fontSizesComputed.value.bodySmall}px`
const _caption = computed(() => props.scaling.font.caption) // WAS: `${settings.fontSizesComputed.value.caption}px`



// Methods
function updateField(fieldName, value) {
  formData.value[fieldName] = value
  emit('update-step-data', props.step.id, { formData: formData.value })
}

async function submitForm() {
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
    
  } catch (error) {
    console.error('Form submission failed:', error)
    // You could add error handling UI here
  } finally {
    isSubmitting.value = false
  }
}

// Computed
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
  return formFields.value.filter(field => formData.value[field.name]).length
})

const canSubmit = computed(() => {
  if (!props.step.form_schema) return false
  
  const requiredFields = getRequiredFields()
  return requiredFields.every(field => formData.value[field.name]) && !isSubmitting.value
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
  if (!activeWorkflow.value?.steps) return null
  
  const allData = {}
  
  // Collect data from all steps
  activeWorkflow.value.steps.forEach((step, index) => {
    if (step.data) {
      Object.assign(allData, step.data)
    }
  })
  
  // Add entity data structure fields (empty if not filled)
  if (activeWorkflow.value.entityDataStructure?.fields) {
    activeWorkflow.value.entityDataStructure.fields.forEach(field => {
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
        <i class="fas fa-edit text-green-600 mr-1"></i>
        Form Details
      </h4>
      <div class="space-y-2">
        <div v-if="formFields.length > 0" :class="['text-sm', colors.secondary.text]">
          {{ formFields.length }} fields to complete
        </div>
        <div v-if="completedFields > 0" class="text-sm">
          <span :class="colors.secondary.text">Completed: </span>
          <span class="font-medium text-green-600 dark:text-green-400">{{ completedFields }}/{{ formFields.length }}</span>
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
    </div>

    <!-- Full Component Mode - For Column 3 Display -->
    <div v-else class="space-y-6">

    <!-- Form Information -->
    <div :class="['rounded-lg border p-4', colors.secondary.bg, colors.secondary.border]">
      <div class="flex justify-between items-start mb-3">
        <h4 :style="{ fontSize: _value }" :class="['font-semibold', colors.primary.text]">
          <i class="fas fa-edit text-green-600 mr-2"></i>
          Form Submission Required
        </h4>
      </div>
      
      <div class="space-y-3">
        <div v-if="props.step.description" :class="colors.secondary.text">
          <p :style="{ fontSize: _bodySmall }">{{ props.step.description }}</p>
        </div>
      </div>
    </div>

    <!-- Form Fields -->
    <div v-if="props.step.form_schema?.sections" class="space-y-6">
      
      <div v-for="section in props.step.form_schema.sections" :key="section.title" 
           :class="['rounded-lg border p-4', colors.primary.bg, colors.secondary.border]">
        
        <h5 :style="{ fontSize: _body }" :class="['font-medium border-b pb-2 mb-4', colors.primary.text, colors.secondary.border]">
          {{ section.title }}
            {{ props.step.form_schema.sections?.length || 0 }} sections with form fields
          
        </h5>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div v-for="field in section.fields" :key="field.name" class="space-y-2">
            <label :style="{ fontSize: _bodySmall }" :class="['block font-medium', colors.primary.textMuted]">
              {{ field.label }}
              <span v-if="field.required" class="text-red-500">*</span>
            </label>
            
            <!-- Text Input -->
            <input v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel' || field.type === 'url'"
                   :type="field.type"
                   :value="formData[field.name] || ''"
                   :style="{ fontSize: _body }"
                   :class="['w-full px-3 py-2 border rounded-lg transition-colors', colors.form.border, colors.form.focus, colors.form.input, colors.primary.text]"
                   :placeholder="`Enter ${field.label.toLowerCase()}`"
                   :disabled="readOnlyPreview"
                   @input="updateField(field.name, $event.target.value)">
            
            <!-- Textarea -->
            <textarea v-else-if="field.type === 'textarea'"
                      :value="formData[field.name] || ''"
                      :style="{ fontSize: _body }"
                      :class="['w-full px-3 py-2 border rounded-lg transition-colors resize-vertical', colors.form.border, colors.form.focus, colors.form.input, colors.primary.text]"
                      :rows="field.attributes?.rows || 3"
                      :placeholder="`Enter ${field.label.toLowerCase()}`"
                      :disabled="readOnlyPreview"
                      @input="updateField(field.name, $event.target.value)"></textarea>
            
            <!-- Select -->
            <select v-else-if="field.type === 'select'"
                    :value="formData[field.name] || ''"
                    :style="{ fontSize: _body }"
                    :class="['w-full px-3 py-2 border rounded-lg transition-colors', colors.form.border, colors.form.focus, colors.form.input, colors.primary.text]"
                    :disabled="readOnlyPreview"
                    @change="updateField(field.name, $event.target.value)">
              <option value="">Select {{ field.label.toLowerCase() }}</option>
              <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
            </select>
            
            <!-- Checkbox -->
            <label v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2 cursor-pointer">
              <input type="checkbox" 
                     :checked="formData[field.name] || false"
                     class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                     :disabled="readOnlyPreview"
                     @change="updateField(field.name, $event.target.checked)">
              <span :style="{ fontSize: _body }" :class="colors.primary.textMuted">
                {{ field.label }}
              </span>
            </label>
            

            <!-- File Upload -->
            <div v-else-if="field.type === 'file'" 
                 class="w-full px-6 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer">
              <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-500 mb-2"></i>
              <p :style="{ fontSize: _bodySmall }" :class="colors.secondary.text">
                Upload {{ field.label.toLowerCase() }}
              </p>
              <p :style="{ fontSize: _caption }" :class="[colors.secondary.text, 'mt-1']">
                {{ field.attributes?.accept || 'All files' }} • Max {{ field.attributes?.maxSize || '10MB' }}
              </p>
            </div>
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
    <div v-if="!readOnlyPreview" class="flex justify-end">
      <button
        @click="submitForm"
        :disabled="!canSubmit"
        :class="['px-6 py-3 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed', colors.form.button]"
        :style="{ fontSize: _body }"
      >
        <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-paper-plane mr-2"></i>
        {{ isSubmitting ? 'Submitting...' : 'Submit Form' }}
      </button>
    </div>
  </div>
</div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
