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
import { useWorkflowSettings } from '../composables/useWorkflowSettings.js'

// Props
const props = defineProps({
  step: {
    type: Object,
    required: true
  },
  stepData: {
    type: Object,
    default: () => ({})
  }
})



// Emits
const emit = defineEmits(['update-step-data', 'step-completed'])

// Composables
const settings = useWorkflowSettings()

// State
const isSubmitting = ref(false)
const formData = ref(props.stepData.formData || {})

// Dynamic font sizes
const _value = computed(() => `${settings.fontSizesComputed.value.h4}px`)
const _body = computed(() => `${settings.fontSizesComputed.value.body}px`)
const _bodySmall = computed(() => `${settings.fontSizesComputed.value.bodySmall}px`)
const _caption = computed(() => `${settings.fontSizesComputed.value.caption}px`)



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
    emit('step-completed', {
      stepId: props.step.id,
      formData: formData.value,
      completedAt: new Date().toISOString()
    })
    
    // Update step data
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
</script>

<template>
  <div class="space-y-6">

    <!-- Form Fields -->
    <div v-if="props.step.form_schema?.sections" class="space-y-6">
      
      <div v-for="section in props.step.form_schema.sections" :key="section.title" 
           class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
        
        <h5 :style="{ fontSize: _body }" class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">
          {{ section.title }}
            {{ props.step.form_schema.sections?.length || 0 }} sections with form fields
          
        </h5>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div v-for="field in section.fields" :key="field.name" class="space-y-2">
            <label :style="{ fontSize: _bodySmall }" class="block font-medium text-gray-700 dark:text-gray-300">
              {{ field.label }}
              <span v-if="field.required" class="text-red-500">*</span>
            </label>
            
            <!-- Text Input -->
            <input v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel' || field.type === 'url'"
                   :type="field.type"
                   :value="formData[field.name] || ''"
                   :style="{ fontSize: _bodySmall }"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                   :placeholder="`Enter ${field.label.toLowerCase()}`"
                   @input="updateField(field.name, $event.target.value)">
            
            <!-- Textarea -->
            <textarea v-else-if="field.type === 'textarea'"
                      :value="formData[field.name] || ''"
                      :style="{ fontSize: _bodySmall }"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
                      :rows="field.attributes?.rows || 3"
                      :placeholder="`Enter ${field.label.toLowerCase()}`"
                      @input="updateField(field.name, $event.target.value)"></textarea>
            
            <!-- Select -->
            <select v-else-if="field.type === 'select'"
                    :value="formData[field.name] || ''"
                    :style="{ fontSize: _bodySmall }"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    @change="updateField(field.name, $event.target.value)">
              <option value="">Select {{ field.label.toLowerCase() }}</option>
              <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
            </select>
            
            <!-- Checkbox -->
            <label v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2 cursor-pointer">
              <input type="checkbox" 
                     :checked="formData[field.name] || false"
                     class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                     @change="updateField(field.name, $event.target.checked)">
              <span :style="{ fontSize: _bodySmall }" class="text-gray-700 dark:text-gray-300">
                {{ field.label }}
              </span>
            </label>
            

            <!-- File Upload -->
            <div v-else-if="field.type === 'file'" 
                 class="w-full px-6 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer">
              <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-500 mb-2"></i>
              <p :style="{ fontSize: _bodySmall }" class="text-gray-600 dark:text-gray-400">
                Upload {{ field.label.toLowerCase() }}
              </p>
              <p :style="{ fontSize: _caption }" class="text-gray-500 dark:text-gray-500 mt-1">
                {{ field.attributes?.accept || 'All files' }} • Max {{ field.attributes?.maxSize || '10MB' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- No Form Schema -->
    <div v-else class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-8 text-center">
      <i class="fas fa-exclamation-triangle text-gray-400 text-3xl mb-4"></i>
      <p :style="{ fontSize: _body }" class="text-gray-500 dark:text-gray-400">
        No form schema defined for this step
      </p>
      <p :style="{ fontSize: _caption }" class="text-gray-400 dark:text-gray-500 mt-1">
        Please configure the form schema in the workflow definition
      </p>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
      <button
        @click="submitForm"
        :disabled="!canSubmit"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :style="{ fontSize: _body }"
      >
        <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
        <i v-else class="fas fa-paper-plane mr-2"></i>
        {{ isSubmitting ? 'Submitting...' : 'Submit Form' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
