/**
 * Workflow Validation Composable
 * Separates validation logic from data files - follows separation of concerns
 */

import { WORKFLOW_STEPTYPES } from '../data/workflowTemplatesConfig.js';

export function useWorkflowValidation() {
  
  /**
   * Validate workflow template structure
   */
  function validateWorkflowTemplate(template) {
    const errors = [];
    
    // Check required fields
    if (!template.id) errors.push('Missing workflow ID');
    if (!template.name) errors.push('Missing workflow name');
    if (!template.description) errors.push('Missing workflow description');
    if (!template.steps) errors.push('Missing steps count');
    if (!template.category) errors.push('Missing category');
    if (!template.complexity) errors.push('Missing complexity level');
    
    // Check complexity values
    if (template.complexity && !['low', 'medium', 'high'].includes(template.complexity)) {
      errors.push(`Invalid complexity level: ${template.complexity}. Must be 'low', 'medium', or 'high'`);
    }
    
    // Check category format
    if (template.category && !template.category.includes('_')) {
      errors.push(`Category should use snake_case format: ${template.category}`);
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validate workflow steps structure and types
   */
  function validateWorkflowSteps(steps) {
    const errors = [];
    
    if (!Array.isArray(steps)) {
      errors.push('Steps must be an array');
      return { isValid: false, errors };
    }
    
    if (steps.length === 0) {
      errors.push('Workflow must have at least one step');
      return { isValid: false, errors };
    }
    
    // Validate each step
    steps.forEach((step, index) => {
      const stepErrors = validateStep(step, index);
      errors.push(...stepErrors);
    });
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validate individual step
   */
  function validateStep(step, index) {
    const errors = [];
    
    // Check required step fields
    if (!step.id) errors.push(`Step ${index + 1}: Missing step ID`);
    if (!step.name) errors.push(`Step ${index + 1}: Missing step name`);
    if (!step.description) errors.push(`Step ${index + 1}: Missing step description`);
    if (!step.type) errors.push(`Step ${index + 1}: Missing step type`);
    if (step.order === undefined || step.order === null) errors.push(`Step ${index + 1}: Missing step order`);
    
    // Validate step type
    if (step.type && !WORKFLOW_STEPTYPES.includes(step.type)) {
      errors.push(`Step ${index + 1}: Invalid step type '${step.type}'. Valid types: ${WORKFLOW_STEPTYPES.join(', ')}`);
    }
    
    // Check step order consistency
    if (step.order !== index + 1) {
      errors.push(`Step ${index + 1}: Order mismatch. Expected ${index + 1}, got ${step.order}`);
    }
    
    // Validate form schema if present
    if (step.form_schema) {
      const schemaErrors = validateFormSchema(step.form_schema, index);
      errors.push(...schemaErrors);
    }
    
    // Validate validation rules if present
    if (step.validation_rules) {
      const validationErrors = validateValidationRules(step.validation_rules, index);
      errors.push(...validationErrors);
    }
    
    return errors;
  }
  
  /**
   * Validate form schema structure
   */
  function validateFormSchema(schema, stepIndex) {
    const errors = [];
    
    if (!schema.sections || !Array.isArray(schema.sections)) {
      errors.push(`Step ${stepIndex + 1}: Form schema must have sections array`);
      return errors;
    }
    
    if (schema.sections.length === 0) {
      errors.push(`Step ${stepIndex + 1}: Form schema must have at least one section`);
    }
    
    schema.sections.forEach((section, sectionIndex) => {
      if (!section.title) {
        errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}: Missing section title`);
      }
      
      if (!section.fields || !Array.isArray(section.fields)) {
        errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}: Missing fields array`);
        return;
      }
      
      if (section.fields.length === 0) {
        errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}: Section must have at least one field`);
      }
      
      section.fields.forEach((field, fieldIndex) => {
        const fieldErrors = validateFormField(field, stepIndex, sectionIndex, fieldIndex);
        errors.push(...fieldErrors);
      });
    });
    
    return errors;
  }
  
  /**
   * Validate form field structure
   */
  function validateFormField(field, stepIndex, sectionIndex, fieldIndex) {
    const errors = [];
    
    if (!field.name) {
      errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}, Field ${fieldIndex + 1}: Missing field name`);
    }
    
    if (!field.type) {
      errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}, Field ${fieldIndex + 1}: Missing field type`);
    }
    
    if (!field.label) {
      errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}, Field ${fieldIndex + 1}: Missing field label`);
    }
    
    // Validate field types
    const validFieldTypes = ['text', 'textarea', 'select', 'multi-select', 'checkbox', 'radio', 'date', 'email', 'tel', 'url', 'file', 'number'];
    if (field.type && !validFieldTypes.includes(field.type)) {
      errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}, Field ${fieldIndex + 1}: Invalid field type '${field.type}'. Valid types: ${validFieldTypes.join(', ')}`);
    }
    
    // Check select fields have options
    if (field.type === 'select' && (!field.options || !Array.isArray(field.options) || field.options.length === 0)) {
      errors.push(`Step ${stepIndex + 1}, Section ${sectionIndex + 1}, Field ${fieldIndex + 1}: Select field must have options array`);
    }
    
    return errors;
  }
  
  /**
   * Validate validation rules structure
   */
  function validateValidationRules(rules, stepIndex) {
    const errors = [];
    
    if (rules.required_fields && !Array.isArray(rules.required_fields)) {
      errors.push(`Step ${stepIndex + 1}: Required fields must be an array`);
    }
    
    if (rules.custom_validations && !Array.isArray(rules.custom_validations)) {
      errors.push(`Step ${stepIndex + 1}: Custom validations must be an array`);
    }
    
    return errors;
  }
  
  /**
   * Comprehensive workflow validation
   */
  function validateWorkflow(workflow) {
    const templateValidation = validateWorkflowTemplate(workflow);
    const stepsValidation = validateWorkflowSteps(workflow.steps || []);
    
    const allErrors = [
      ...templateValidation.errors,
      ...stepsValidation.errors
    ];
    
    return {
      isValid: templateValidation.isValid && stepsValidation.isValid,
      errors: allErrors,
      templateValid: templateValidation.isValid,
      stepsValid: stepsValidation.isValid
    };
  }
  
  /**
   * Validate step types against allowed types
   */
  function validateStepTypes(steps) {
    const invalidTypes = [];
    
    steps.forEach((step, index) => {
      if (!WORKFLOW_STEPTYPES.includes(step.type)) {
        invalidTypes.push({
          stepIndex: index,
          stepName: step.name,
          invalidType: step.type,
          validTypes: WORKFLOW_STEPTYPES
        });
      }
    });
    
    return {
      isValid: invalidTypes.length === 0,
      invalidTypes
    };
  }
  
  return {
    validateWorkflowTemplate,
    validateWorkflowSteps,
    validateStep,
    validateFormSchema,
    validateFormField,
    validateValidationRules,
    validateWorkflow,
    validateStepTypes
  };
}
