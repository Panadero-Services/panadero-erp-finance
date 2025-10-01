/**
 * Generic Form Validation Composable
 * @version 1.0.0
 * @description Reusable validation system for any form
 */

import { ref, computed, watch, readonly } from 'vue'
import { validateField, parseRules } from '@/utils/validators'

export function useFormValidation(validationSchema = {}) {
  // Validation errors state
  const validationErrors = ref({})
  
  // Validation schema - can be passed in or set later
  const schema = ref(validationSchema)
  
  // Set validation schema
  const setSchema = (newSchema) => {
    schema.value = newSchema
  }
  
  // Validate a single field
  const validateFieldValue = (fieldName, value, customRules = null) => {
    const fieldSchema = customRules || schema.value[fieldName]
    if (!fieldSchema) return null
    
    // Parse rules if they're in string format
    const rules = typeof fieldSchema === 'string' ? parseRules(fieldSchema) : fieldSchema
    
    // Check if field is valid
    const isValid = validateField(value, rules)
    
    if (!isValid) {
      // Return appropriate error message
      return getErrorMessage(fieldName, rules, value)
    }
    
    return null
  }
  
  // Get error message for a field
  const getErrorMessage = (fieldName, rules, value) => {
    // Check each rule to determine which one failed
    for (const [rule, param] of Object.entries(rules)) {
      switch (rule) {
        case 'required':
          if (value === null || value === undefined || value.toString().trim() === '') {
            return `${getFieldLabel(fieldName)} is required`
          }
          break
        case 'numeric':
          if (isNaN(value)) {
            return `${getFieldLabel(fieldName)} must be a number`
          }
          break
        case 'email':
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
          if (!emailRegex.test(value)) {
            return `${getFieldLabel(fieldName)} must be a valid email address`
          }
          break
        case 'alpha':
          const alphaRegex = /^[A-Za-z]+$/
          if (!alphaRegex.test(value)) {
            return `${getFieldLabel(fieldName)} must contain only letters`
          }
          break
        case 'min':
          if (typeof value === 'string' && value.length < +param) {
            return `${getFieldLabel(fieldName)} must be at least ${param} characters`
          }
          if (typeof value === 'number' && value < +param) {
            return `${getFieldLabel(fieldName)} must be at least ${param}`
          }
          break
        case 'max':
          if (typeof value === 'string' && value.length > +param) {
            return `${getFieldLabel(fieldName)} must be no more than ${param} characters`
          }
          if (typeof value === 'number' && value > +param) {
            return `${getFieldLabel(fieldName)} must be no more than ${param}`
          }
          break
        case 'alphanumeric':
          const alphanumericRegex = /^[A-Za-z0-9_-]+$/
          if (!alphanumericRegex.test(value)) {
            return `${getFieldLabel(fieldName)} can only contain letters, numbers, hyphens, and underscores`
          }
          break
        case 'url':
          const urlRegex = /^https?:\/\/.+\..+/
          if (!urlRegex.test(value)) {
            return `${getFieldLabel(fieldName)} must be a valid URL`
          }
          break
        case 'phone':
          const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/
          if (!phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
            return `${getFieldLabel(fieldName)} must be a valid phone number`
          }
          break
      }
    }
    
    return `${getFieldLabel(fieldName)} is invalid`
  }
  
  // Get human-readable field label
  const getFieldLabel = (fieldName) => {
    // Convert snake_case to Title Case
    return fieldName
      .split('_')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ')
  }
  
  // Validate all fields in a form data object
  const validateForm = (formData) => {
    const errors = {}
    let isValid = true
    
    Object.keys(schema.value).forEach(fieldName => {
      const error = validateFieldValue(fieldName, formData[fieldName])
      if (error) {
        errors[fieldName] = error
        isValid = false
      }
    })
    
    validationErrors.value = errors
    return isValid
  }
  
  // Validate a single field and update errors
  const validateSingleField = (fieldName, value) => {
    const error = validateFieldValue(fieldName, value)
    
    if (error) {
      validationErrors.value[fieldName] = error
    } else {
      delete validationErrors.value[fieldName]
    }
    
    return !error
  }
  
  // Clear all validation errors
  const clearErrors = () => {
    validationErrors.value = {}
  }
  
  // Clear error for specific field
  const clearFieldError = (fieldName) => {
    delete validationErrors.value[fieldName]
  }
  
  // Check if form is valid
  const isFormValid = computed(() => {
    return Object.keys(validationErrors.value).length === 0
  })
  
  // Check if specific field has error
  const hasFieldError = (fieldName) => {
    return !!validationErrors.value[fieldName]
  }
  
  // Get error message for specific field
  const getFieldError = (fieldName) => {
    return validationErrors.value[fieldName] || null
  }
  
  // Get all validation errors
  const getAllErrors = () => {
    return validationErrors.value
  }
  
  // Get first error message
  const getFirstError = () => {
    const errors = Object.values(validationErrors.value)
    return errors.length > 0 ? errors[0] : null
  }
  
  // Watch form data and validate in real-time
  const watchFormData = (formData) => {
    watch(formData, (newData) => {
      // Only validate fields that have been touched or have errors
      Object.keys(schema.value).forEach(fieldName => {
        if (validationErrors.value[fieldName] || newData[fieldName] !== undefined) {
          validateSingleField(fieldName, newData[fieldName])
        }
      })
    }, { deep: true })
  }
  
  // Create validation rules from Laravel validation rules
  const createSchemaFromLaravelRules = (laravelRules) => {
    const schema = {}
    
    Object.entries(laravelRules).forEach(([field, rules]) => {
      if (typeof rules === 'string') {
        schema[field] = rules
      } else if (Array.isArray(rules)) {
        schema[field] = rules.join('|')
      } else {
        schema[field] = rules
      }
    })
    
    return schema
  }
  
  return {
    // State
    validationErrors: readonly(validationErrors),
    schema,
    
    // Methods
    setSchema,
    validateFieldValue,
    validateForm,
    validateSingleField,
    clearErrors,
    clearFieldError,
    watchFormData,
    createSchemaFromLaravelRules,
    
    // Computed
    isFormValid,
    hasFieldError,
    getFieldError,
    getAllErrors,
    getFirstError
  }
}
