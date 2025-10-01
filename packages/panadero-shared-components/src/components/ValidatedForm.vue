<!--
  Generic Validated Form Component
  @version 1.0.1
  @description Generic form wrapper with validation support for ERP packages
-->
<script setup>
import { ref, computed, provide, watch } from 'vue'
import { useFormValidation } from '../composables/useFormValidation.js'

const props = defineProps({
  validationSchema: {
    type: Object,
    default: () => ({})
  },
  formData: {
    type: Object,
    required: true
  },
  showValidation: {
    type: Boolean,
    default: true
  },
  // Allow custom validation mode
  validationMode: {
    type: String,
    default: 'realtime', // 'realtime', 'onsubmit', 'manual'
    validator: (value) => ['realtime', 'onsubmit', 'manual'].includes(value)
  },
  // Allow custom submit behavior
  preventSubmit: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'update:formData', 'validation-change'])

// Initialize validation
const {
  validationErrors,
  isFormValid,
  hasFieldError,
  getFieldError,
  validateForm,
  validateSingleField,
  clearErrors,
  watchFormData
} = useFormValidation(props.validationSchema)

// Watch form data for real-time validation (if enabled)
if (props.validationMode === 'realtime') {
  watchFormData(props.formData)
}

// Provide validation functions to child components
provide('validation', {
  hasFieldError,
  getFieldError,
  validateSingleField,
  clearErrors,
  isFormValid
})

// Handle form submission
const handleSubmit = (event) => {
  if (props.preventSubmit) {
    event.preventDefault()
  }
  
  if (props.validationMode !== 'manual') {
    if (validateForm(props.formData)) {
      emit('submit', props.formData)
    }
  } else {
    emit('submit', props.formData)
  }
}

// Watch for validation changes
watch(validationErrors, (newErrors) => {
  emit('validation-change', {
    errors: newErrors,
    isValid: isFormValid.value
  })
}, { deep: true })

// Expose validation state and methods
defineExpose({
  isFormValid,
  validateForm,
  clearErrors,
  validationErrors,
  hasFieldError,
  getFieldError
})
</script>

<template>
  <form @submit.prevent="handleSubmit" class="validated-form">
    <slot 
      :isFormValid="isFormValid"
      :hasFieldError="hasFieldError"
      :getFieldError="getFieldError"
      :validationErrors="validationErrors"
      :validateForm="validateForm"
      :clearErrors="clearErrors"
    />
  </form>
</template>

<style scoped>
.validated-form {
  @apply space-y-4;
}
</style>