/**
 * Step Type Handler Composable
 * Wrapper for handling different step types in WorkflowForm.vue
 */

import { computed, ref } from 'vue';

export function useStepTypeHandler(step, stepData, emit) {
  
  // Reactive state for step handling
  const isProcessing = ref(false);
  const validationErrors = ref([]);
  const stepStatus = ref('pending'); // pending, processing, completed, error
  
  // Computed properties for step type handling
  const stepTypeConfig = computed(() => {
    switch (step.type) {
      case 'form_submission':
        return {
          component: 'FormSubmission',
          icon: 'fas fa-edit',
          color: 'blue',
          description: 'Form-based data collection',
          canEdit: true,
          requiresValidation: true
        };
        
      case 'approval':
        return {
          component: 'ApprovalStep',
          icon: 'fas fa-check-circle',
          color: 'green',
          description: 'Approval required',
          canEdit: false,
          requiresValidation: false
        };
        
      case 'checklist':
        return {
          component: 'ChecklistStep',
          icon: 'fas fa-list-check',
          color: 'purple',
          description: 'Checklist completion',
          canEdit: true,
          requiresValidation: true
        };
        
      case 'shared_entity_selection':
        return {
          component: 'EntitySelection',
          icon: 'fas fa-users',
          color: 'indigo',
          description: 'Select from available entities',
          canEdit: true,
          requiresValidation: true
        };
        
      case 'submit_database':
        return {
          component: 'DatabaseSubmission',
          icon: 'fas fa-database',
          color: 'orange',
          description: 'Submit data to database',
          canEdit: true,
          requiresValidation: true
        };
        
      default:
        return {
          component: 'DefaultStep',
          icon: 'fas fa-question-circle',
          color: 'gray',
          description: 'Unknown step type',
          canEdit: false,
          requiresValidation: false
        };
    }
  });
  
  // Step type specific handlers
  const handlers = {
    form_submission: {
      async process(data) {
        isProcessing.value = true;
        validationErrors.value = [];
        
        try {
          // Validate form data
          const validation = validateFormData(data);
          if (!validation.isValid) {
            validationErrors.value = validation.errors;
            stepStatus.value = 'error';
            return { success: false, errors: validation.errors };
          }
          
          // Process form submission
          const result = await submitFormData(data);
          stepStatus.value = 'completed';
          emit('step-completed', { stepId: step.id, data: result });
          
          return { success: true, data: result };
        } catch (error) {
          stepStatus.value = 'error';
          validationErrors.value = [error.message];
          return { success: false, error: error.message };
        } finally {
          isProcessing.value = false;
        }
      },
      
      validate(data) {
        return validateFormData(data);
      }
    },
    
    approval: {
      async process(data) {
        isProcessing.value = true;
        
        try {
          // For approval steps, we just mark as pending approval
          stepStatus.value = 'pending';
          emit('approval-requested', { stepId: step.id, data });
          
          return { success: true, status: 'pending_approval' };
        } catch (error) {
          stepStatus.value = 'error';
          return { success: false, error: error.message };
        } finally {
          isProcessing.value = false;
        }
      },
      
      validate(data) {
        // Approval steps don't need data validation
        return { isValid: true, errors: [] };
      }
    },
    
    checklist: {
      async process(data) {
        isProcessing.value = true;
        validationErrors.value = [];
        
        try {
          // Validate checklist completion
          const validation = validateChecklist(data);
          if (!validation.isValid) {
            validationErrors.value = validation.errors;
            stepStatus.value = 'error';
            return { success: false, errors: validation.errors };
          }
          
          // Process checklist
          const result = await processChecklist(data);
          stepStatus.value = 'completed';
          emit('step-completed', { stepId: step.id, data: result });
          
          return { success: true, data: result };
        } catch (error) {
          stepStatus.value = 'error';
          validationErrors.value = [error.message];
          return { success: false, error: error.message };
        } finally {
          isProcessing.value = false;
        }
      },
      
      validate(data) {
        return validateChecklist(data);
      }
    },
    
    shared_entity_selection: {
      async process(data) {
        isProcessing.value = true;
        validationErrors.value = [];
        
        try {
          // Validate entity selection
          const validation = validateEntitySelection(data);
          if (!validation.isValid) {
            validationErrors.value = validation.errors;
            stepStatus.value = 'error';
            return { success: false, errors: validation.errors };
          }
          
          // Process entity selection
          const result = await processEntitySelection(data);
          stepStatus.value = 'completed';
          emit('step-completed', { stepId: step.id, data: result });
          
          return { success: true, data: result };
        } catch (error) {
          stepStatus.value = 'error';
          validationErrors.value = [error.message];
          return { success: false, error: error.message };
        } finally {
          isProcessing.value = false;
        }
      },
      
      validate(data) {
        return validateEntitySelection(data);
      }
    },
    
    submit_database: {
      async process(data) {
        isProcessing.value = true;
        validationErrors.value = [];
        
        try {
          // Validate database submission data
          const validation = validateDatabaseData(data);
          if (!validation.isValid) {
            validationErrors.value = validation.errors;
            stepStatus.value = 'error';
            return { success: false, errors: validation.errors };
          }
          
          // Submit to database
          const result = await submitToDatabase(data);
          stepStatus.value = 'completed';
          emit('step-completed', { stepId: step.id, data: result });
          
          return { success: true, data: result };
        } catch (error) {
          stepStatus.value = 'error';
          validationErrors.value = [error.message];
          return { success: false, error: error.message };
        } finally {
          isProcessing.value = false;
        }
      },
      
      validate(data) {
        return validateDatabaseData(data);
      }
    }
  };
  
  // Main process method that delegates to specific handlers
  async function processStep(data) {
    const handler = handlers[step.type];
    if (!handler) {
      throw new Error(`No handler found for step type: ${step.type}`);
    }
    
    return await handler.process(data);
  }
  
  // Main validate method that delegates to specific handlers
  function validateStep(data) {
    const handler = handlers[step.type];
    if (!handler) {
      return { isValid: false, errors: [`No handler found for step type: ${step.type}`] };
    }
    
    return handler.validate(data);
  }
  
  // Validation helper functions
  function validateFormData(data) {
    const errors = [];
    
    if (step.form_schema?.sections) {
      step.form_schema.sections.forEach(section => {
        section.fields.forEach(field => {
          if (field.required && (!data[field.name] || data[field.name] === '')) {
            errors.push(`${field.label} is required`);
          }
        });
      });
    }
    
    return { isValid: errors.length === 0, errors };
  }
  
  function validateChecklist(data) {
    const errors = [];
    
    if (step.form_schema?.sections) {
      step.form_schema.sections.forEach(section => {
        section.fields.forEach(field => {
          if (field.required && !data[field.name]) {
            errors.push(`${field.label} must be completed`);
          }
        });
      });
    }
    
    return { isValid: errors.length === 0, errors };
  }
  
  function validateEntitySelection(data) {
    const errors = [];
    
    if (!data.selectedEntity || !data.selectedEntity.id) {
      errors.push('An entity must be selected');
    }
    
    return { isValid: errors.length === 0, errors };
  }
  
  function validateDatabaseData(data) {
    const errors = [];
    
    if (step.form_schema?.sections) {
      step.form_schema.sections.forEach(section => {
        section.fields.forEach(field => {
          if (field.required && (!data[field.name] || data[field.name] === '')) {
            errors.push(`${field.label} is required`);
          }
        });
      });
    }
    
    return { isValid: errors.length === 0, errors };
  }
  
  // Mock async functions (replace with actual implementations)
  async function submitFormData(data) {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500));
    return { submitted: true, timestamp: new Date().toISOString() };
  }
  
  async function processChecklist(data) {
    await new Promise(resolve => setTimeout(resolve, 300));
    return { completed: true, items: Object.keys(data).filter(key => data[key]) };
  }
  
  async function processEntitySelection(data) {
    await new Promise(resolve => setTimeout(resolve, 200));
    return { selected: data.selectedEntity, timestamp: new Date().toISOString() };
  }
  
  async function submitToDatabase(data) {
    await new Promise(resolve => setTimeout(resolve, 1000));
    return { submitted: true, recordId: `DB_${Date.now()}`, timestamp: new Date().toISOString() };
  }
  
  return {
    // State
    isProcessing,
    validationErrors,
    stepStatus,
    
    // Computed
    stepTypeConfig,
    
    // Methods
    processStep,
    validateStep,
    
    // Individual handlers for direct access if needed
    handlers
  };
}
