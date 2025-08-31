<!--
  Workflow Checklist Component
  @version 1.1.0
  @description Handles checklist step types in workflows - step type specific
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

// Dynamic font sizes
const _value = computed(() => `${settings.fontSizesComputed.value.h4}px`)
const _body = computed(() => `${settings.fontSizesComputed.value.body}px`)
const _bodySmall = computed(() => `${settings.fontSizesComputed.value.bodySmall}px`)

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
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h3 :style="{ fontSize: _value }" class="font-medium text-gray-900 dark:text-white">
        {{ step.name }}
      </h3>
      <span :class="['px-2 py-1 rounded-full text-xs font-medium', progressClass]">
        {{ progress }}% Complete
      </span>
    </div>

    <!-- Description -->
    <p :style="{ fontSize: _body }" class="text-gray-600 dark:text-gray-400">
      {{ step.description }}
    </p>

    <!-- Checklist Items (only checkboxes from template) -->
    <div class="space-y-4">
      <h4 :style="{ fontSize: _body }" class="font-medium text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 pb-2">
        Checklist Items
      </h4>
      
      <div class="space-y-3">
        <div 
          v-for="field in checkboxFields" 
          :key="field.name" 
          class="flex items-start space-x-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
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
            :style="{ fontSize: _bodySmall }" 
            class="flex-1 font-medium text-gray-700 dark:text-gray-300 cursor-pointer"
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
      <label :style="{ fontSize: _bodySmall }" class="block font-medium text-gray-700 dark:text-gray-300">
        Comments (Optional)
      </label>
      <textarea 
        :value="comments"
        @input="comments = $event.target.value"
        rows="3"
        placeholder="Add any additional notes or comments..."
        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        :style="{ fontSize: _bodySmall }"
      ></textarea>
    </div>

    <!-- Progress and Submit -->
    <div class="pt-4 border-t border-gray-200 dark:border-gray-600 space-y-3">
      <!-- Progress Bar -->
      <div class="space-y-1">
        <div class="flex justify-between text-sm">
          <span class="text-gray-600 dark:text-gray-400">Progress</span>
          <span class="text-gray-900 dark:text-white font-medium">{{ progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        @click="submitChecklist"
        :disabled="!canSubmit"
        :class="[
          'w-full px-4 py-3 rounded-lg font-medium transition-colors',
          canSubmit 
            ? 'bg-blue-600 hover:bg-blue-700 text-white' 
            : 'bg-gray-300 text-gray-500 cursor-not-allowed'
        ]"
        :style="{ fontSize: _body }"
      >
        {{ isSubmitting ? 'Submitting...' : 'Complete Checklist' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Pure Tailwind CSS - no custom styles needed */
</style>
