<script setup>
import { ref, computed, toRef } from 'vue'
import { useFinanceStore } from '../../stores/financeStore'
import FinanceInput from '../ui/FinanceInput.vue'
import FinanceDropdown from '../ui/FinanceDropdown.vue'
import FinanceButton from '../ui/FinanceButton.vue'

const props = defineProps({
  schema: {
    type: Object,
    required: true
  },
  modelValue: {
    type: Object,
    default: () => ({})
  },
  readonly: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue', 'submit', 'file-upload'])

const store = useFinanceStore()
const fileInputs = ref({})

// Use direct prop binding with computed getter/setter pattern (no watchers!)
const formData = computed({
  get: () => props.modelValue || {},
  set: (value) => emit('update:modelValue', value)
})

// Computed validation state
const isValid = computed(() => {
  if (!props.schema.sections) return true
  
  let valid = true
  props.schema.sections.forEach(section => {
    section.fields.forEach(field => {
      if (field.required && (!formData.value[field.name] || formData.value[field.name] === '')) {
        valid = false
      }
    })
  })
  return valid
})

// Get field value
const getFieldValue = (fieldName) => {
  return formData.value[fieldName] || ''
}

// Set field value
const setFieldValue = (fieldName, value) => {
  // Create new object to trigger reactivity properly
  formData.value = {
    ...formData.value,
    [fieldName]: value
  }
}

// Handle file upload
const handleFileUpload = (fieldName, event) => {
  const files = event.target.files
  if (files && files.length > 0) {
    const file = files[0]
    setFieldValue(fieldName, file.name) // Store filename in form data
    emit('file-upload', { field: fieldName, file: file })
  }
}

// Get field error
const getFieldError = (fieldName) => {
  return props.errors[fieldName] || ''
}

// Handle form submission
const handleSubmit = () => {
  if (isValid.value) {
    emit('submit', formData.value)
  }
}

// Get dropdown options
const getDropdownOptions = (field) => {
  if (!field.options) return []
  return field.options.map(option => ({
    label: option,
    value: option
  }))
}

// Get input type mapping
const getInputType = (fieldType) => {
  const typeMap = {
    'text': 'text',
    'email': 'email',
    'tel': 'tel',
    'url': 'url',
    'number': 'number',
    'password': 'password',
    'search': 'search'
  }
  return typeMap[fieldType] || 'text'
}
</script>

<template>
  <div class="workflow-form">
    <form @submit.prevent="handleSubmit">
      <!-- Form Sections -->
      <div 
        v-for="(section, sectionIndex) in schema.sections" 
        :key="sectionIndex"
        class="form-section"
      >
        <!-- Section Title -->
        <h3 
          class="section-title"
          :style="{ fontSize: `${store.fontSizes.base + 2}px` }"
        >
          {{ section.title }}
        </h3>

        <!-- Section Description -->
        <p 
          v-if="section.description"
          class="section-description"
          :style="{ fontSize: `${store.fontSizes.base - 1}px` }"
        >
          {{ section.description }}
        </p>

        <!-- Form Fields Grid -->
        <div class="fields-grid">
          <div 
            v-for="field in section.fields" 
            :key="field.name"
            class="field-container"
            :class="{
              'field-full-width': field.type === 'textarea' || field.full_width,
              'field-half-width': field.type === 'checkbox' || field.half_width
            }"
          >
            <!-- Text Input -->
            <FinanceInput
              v-if="['text', 'email', 'tel', 'url', 'number', 'password', 'search'].includes(field.type)"
              :model-value="getFieldValue(field.name)"
              :type="getInputType(field.type)"
              :label="field.label"
              :placeholder="field.placeholder || field.label"
              :required="field.required"
              :disabled="readonly || field.readonly"
              :error="getFieldError(field.name)"
              :help-text="field.help_text"
              :icon-left="field.icon"
              variant="outline"
              size="normal"
              @update:model-value="setFieldValue(field.name, $event)"
            />

            <!-- Textarea -->
            <div v-else-if="field.type === 'textarea'" class="field-wrapper">
              <label 
                v-if="field.label"
                class="field-label"
                :style="{ fontSize: `${store.fontSizes.base - 2}px` }"
              >
                {{ field.label }}
                <span v-if="field.required" class="text-red-500 ml-1">*</span>
              </label>
              <textarea
                :value="getFieldValue(field.name)"
                :placeholder="field.placeholder || field.label"
                :required="field.required"
                :disabled="readonly || field.readonly"
                :rows="field.rows || 3"
                class="finance-textarea"
                :style="{
                  fontSize: `${store.fontSizes.base}px`,
                  ...store.scalingStyles.buttonPadding
                }"
                @input="setFieldValue(field.name, $event.target.value)"
              />
              <p 
                v-if="getFieldError(field.name)"
                class="field-error"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ getFieldError(field.name) }}
              </p>
              <p 
                v-else-if="field.help_text"
                class="field-help"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ field.help_text }}
              </p>
            </div>

            <!-- Select Dropdown -->
            <FinanceDropdown
              v-else-if="field.type === 'select'"
              :model-value="getFieldValue(field.name)"
              :options="getDropdownOptions(field)"
              :placeholder="field.placeholder || `Select ${field.label}`"
              :disabled="readonly || field.readonly"
              option-label="label"
              option-value="value"
              variant="outline"
              size="normal"
              @update:model-value="setFieldValue(field.name, $event)"
            />

            <!-- Multi-Select -->
            <div v-else-if="field.type === 'multi-select'" class="field-wrapper">
              <label 
                v-if="field.label"
                class="field-label"
                :style="{ fontSize: `${store.fontSizes.base - 2}px` }"
              >
                {{ field.label }}
                <span v-if="field.required" class="text-red-500 ml-1">*</span>
              </label>
              <div class="multi-select-container">
                <div 
                  v-for="option in field.options"
                  :key="option"
                  class="multi-select-option"
                >
                  <input
                    :id="`${field.name}_${option}`"
                    type="checkbox"
                    :value="option"
                    :checked="(getFieldValue(field.name) || []).includes(option)"
                    :disabled="readonly || field.readonly"
                    class="checkbox-input"
                    @change="handleMultiSelectChange(field.name, option, $event.target.checked)"
                  />
                  <label 
                    :for="`${field.name}_${option}`"
                    class="checkbox-label"
                    :style="{ fontSize: `${store.fontSizes.base - 1}px` }"
                  >
                    {{ option }}
                  </label>
                </div>
              </div>
              <p 
                v-if="getFieldError(field.name)"
                class="field-error"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ getFieldError(field.name) }}
              </p>
            </div>

            <!-- Checkbox -->
            <div v-else-if="field.type === 'checkbox'" class="field-wrapper">
              <div class="checkbox-container">
                <input
                  :id="field.name"
                  type="checkbox"
                  :checked="getFieldValue(field.name)"
                  :disabled="readonly || field.readonly"
                  :required="field.required"
                  class="checkbox-input"
                  @change="setFieldValue(field.name, $event.target.checked)"
                />
                <label 
                  :for="field.name"
                  class="checkbox-label"
                  :style="{ fontSize: `${store.fontSizes.base - 1}px` }"
                >
                  {{ field.label }}
                  <span v-if="field.required" class="text-red-500 ml-1">*</span>
                </label>
              </div>
              <p 
                v-if="field.help_text"
                class="field-help"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ field.help_text }}
              </p>
              <p 
                v-if="getFieldError(field.name)"
                class="field-error"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ getFieldError(field.name) }}
              </p>
            </div>

            <!-- File Upload -->
            <div v-else-if="field.type === 'file'" class="field-wrapper">
              <label 
                v-if="field.label"
                class="field-label"
                :style="{ fontSize: `${store.fontSizes.base - 2}px` }"
              >
                {{ field.label }}
                <span v-if="field.required" class="text-red-500 ml-1">*</span>
              </label>
              <div class="file-upload-container">
                <input
                  :ref="el => fileInputs[field.name] = el"
                  type="file"
                  :accept="field.accept"
                  :disabled="readonly || field.readonly"
                  :required="field.required"
                  class="file-input"
                  @change="handleFileUpload(field.name, $event)"
                />
                <div class="file-upload-button">
                  <FinanceButton
                    variant="outline-secondary"
                    size="normal"
                    icon-left="fas fa-upload"
                    :disabled="readonly || field.readonly"
                    @click="fileInputs[field.name]?.click()"
                  >
                    Choose File
                  </FinanceButton>
                  <span 
                    v-if="getFieldValue(field.name)"
                    class="file-name"
                    :style="{ fontSize: `${store.fontSizes.base - 2}px` }"
                  >
                    {{ getFieldValue(field.name) }}
                  </span>
                </div>
              </div>
              <p 
                v-if="field.help_text"
                class="field-help"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ field.help_text }}
              </p>
              <p 
                v-if="getFieldError(field.name)"
                class="field-error"
                :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
              >
                {{ getFieldError(field.name) }}
              </p>
            </div>

            <!-- Date Input -->
            <FinanceInput
              v-else-if="field.type === 'date'"
              :model-value="getFieldValue(field.name)"
              type="date"
              :label="field.label"
              :required="field.required"
              :disabled="readonly || field.readonly"
              :error="getFieldError(field.name)"
              :help-text="field.help_text"
              variant="outline"
              size="normal"
              @update:model-value="setFieldValue(field.name, $event)"
            />
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div v-if="!readonly" class="form-actions">
        <FinanceButton
          type="submit"
          variant="primary"
          size="normal"
          :disabled="!isValid"
          icon-left="fas fa-save"
        >
          Save & Continue
        </FinanceButton>
        
        <FinanceButton
          variant="outline-secondary"
          size="normal"
          icon-left="fas fa-times"
          @click="$emit('cancel')"
        >
          Cancel
        </FinanceButton>
      </div>

      <!-- Form Summary (readonly mode) -->
      <div v-if="readonly" class="form-summary">
        <p class="summary-text" :style="{ fontSize: `${store.fontSizes.base - 1}px` }">
          <i class="fas fa-info-circle mr-2"></i>
          Form data is read-only in this view
        </p>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  methods: {
    handleMultiSelectChange(fieldName, option, checked) {
      const currentValue = this.getFieldValue(fieldName) || []
      let newValue
      
      if (checked) {
        newValue = [...currentValue, option]
      } else {
        newValue = currentValue.filter(item => item !== option)
      }
      
      this.setFieldValue(fieldName, newValue)
    }
  }
}
</script>

<style scoped>
/* Form Layout */
.workflow-form {
  @apply w-full max-w-4xl mx-auto;
}

.form-section {
  @apply mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700;
}

.section-title {
  @apply font-bold text-gray-900 dark:text-white mb-2;
}

.section-description {
  @apply text-gray-600 dark:text-gray-400 mb-6;
}

/* Fields Grid */
.fields-grid {
  @apply grid grid-cols-1 md:grid-cols-2 gap-4;
}

.field-container.field-full-width {
  @apply col-span-1 md:col-span-2;
}

.field-container.field-half-width {
  @apply col-span-1;
}

/* Field Components */
.field-wrapper {
  @apply w-full;
}

.field-label {
  @apply block font-medium text-gray-700 dark:text-gray-300 mb-1;
}

.field-error {
  @apply text-red-500 dark:text-red-400 mt-1;
}

.field-help {
  @apply text-gray-500 dark:text-gray-400 mt-1;
}

/* Textarea */
.finance-textarea {
  @apply w-full font-medium rounded-lg transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
  background-color: transparent !important;
  border: 2px solid #6366f1 !important;
  color: #6366f1 !important;
  resize: vertical;
  min-height: 80px;
}

.finance-textarea::placeholder {
  color: rgba(99, 102, 241, 0.6) !important;
}

.finance-textarea:hover {
  background-color: rgba(99, 102, 241, 0.05) !important;
  border-color: #4f46e5 !important;
  color: #4f46e5 !important;
}

.finance-textarea:focus {
  @apply ring-indigo-500;
  border-color: #4f46e5 !important;
}

.dark .finance-textarea {
  border-color: #60a5fa !important;
  color: #60a5fa !important;
}

.dark .finance-textarea::placeholder {
  color: rgba(96, 165, 250, 0.6) !important;
}

.dark .finance-textarea:hover {
  background-color: rgba(96, 165, 250, 0.05) !important;
  border-color: #3b82f6 !important;
  color: #3b82f6 !important;
}

/* Multi-Select */
.multi-select-container {
  @apply space-y-2 p-3 border-2 border-indigo-500 rounded-lg bg-transparent;
}

.multi-select-option {
  @apply flex items-center space-x-2;
}

/* Checkbox */
.checkbox-container {
  @apply flex items-start space-x-2;
}

.checkbox-input {
  @apply w-4 h-4 text-indigo-600 border-2 border-indigo-500 rounded focus:ring-indigo-500 focus:ring-2;
}

.checkbox-label {
  @apply text-gray-700 dark:text-gray-300 cursor-pointer leading-tight;
}

/* File Upload */
.file-upload-container {
  @apply relative;
}

.file-input {
  @apply absolute inset-0 w-full h-full opacity-0 cursor-pointer;
}

.file-upload-button {
  @apply flex items-center space-x-3 p-3 border-2 border-dashed border-indigo-300 rounded-lg hover:border-indigo-400 transition-colors;
}

.file-name {
  @apply text-gray-600 dark:text-gray-400 truncate;
}

/* Form Actions */
.form-actions {
  @apply flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700;
}

/* Form Summary */
.form-summary {
  @apply mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800;
}

.summary-text {
  @apply text-blue-700 dark:text-blue-300 flex items-center;
}

/* Dark mode adjustments */
.dark .finance-textarea:focus {
  @apply ring-offset-gray-800;
}

.dark .checkbox-input {
  @apply bg-gray-800 border-blue-400;
}

.dark .file-upload-button {
  @apply border-blue-400 hover:border-blue-300;
}

/* Responsive Design */
@media (max-width: 768px) {
  .fields-grid {
    @apply grid-cols-1;
  }
  
  .field-container.field-full-width,
  .field-container.field-half-width {
    @apply col-span-1;
  }
  
  .form-actions {
    @apply flex-col space-y-3 space-x-0;
  }
}
</style>
