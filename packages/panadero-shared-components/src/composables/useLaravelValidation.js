/**
 * Laravel Validation Integration Composable
 * @version 1.0.0
 * @description Automatically sync validation rules with Laravel backend
 */

import { ref, computed } from 'vue'
import { useFormValidation } from './useFormValidation.js'

export function useLaravelValidation(modelName) {
  const validationRules = ref({})
  const isLoading = ref(false)
  
  // Fetch validation rules from Laravel backend
  const fetchValidationRules = async () => {
    if (!modelName) return
    
    isLoading.value = true
    try {
      const response = await fetch(`/api/${modelName}/validation-rules`)
      if (response.ok) {
        const rules = await response.json()
        validationRules.value = rules
      }
    } catch (error) {
      console.warn('Could not fetch validation rules from backend:', error)
    } finally {
      isLoading.value = false
    }
  }
  
  // Initialize validation with fetched rules
  const {
    validationErrors,
    isFormValid,
    hasFieldError,
    getFieldError,
    validateForm,
    validateSingleField,
    clearErrors,
    watchFormData,
    createSchemaFromLaravelRules
  } = useFormValidation()
  
  // Convert Laravel rules to frontend schema
  const frontendSchema = computed(() => {
    return createSchemaFromLaravelRules(validationRules.value)
  })
  
  // Auto-fetch rules when model name changes
  if (modelName) {
    fetchValidationRules()
  }
  
  return {
    // State
    validationRules,
    isLoading,
    frontendSchema,
    
    // Validation functions
    validationErrors,
    isFormValid,
    hasFieldError,
    getFieldError,
    validateForm,
    validateSingleField,
    clearErrors,
    watchFormData,
    
    // Methods
    fetchValidationRules
  }
}
