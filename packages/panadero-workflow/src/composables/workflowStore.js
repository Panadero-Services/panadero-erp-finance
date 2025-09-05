/**
 * Financial ERP Workflow Management Store
 * @version 1.1.2
 * @date 26-Aug-2025
 * @description Comprehensive workflow management system with database persistence
 */
import { defineStore } from 'pinia';
import { ref, computed, readonly } from 'vue';
import axios from 'axios';
import { getAllWorkflowTemplates } from '../data/workflowTemplatesConfig.js';
import { useWorkflowValidation } from './useWorkflowValidation.js';

// Add this at the top after the imports
let nextWorkflowId = 1

export const useWorkflowStore = defineStore('workflow', () => {
  // Settings - centralized configuration
  // 22.08.2025 needs work !! --> standarardize over modules and put in generic composable
  const settings = ref({
    // Font scaling
    fontSize: 14,
    
    // Display
    darkMode: true,
    compactLayout: false,
    showDebug: false,
    
    // Tables
    rowsPerPage: 25,
    showRowNumbers: true,
    stickyHeaders: true,
    
    // Export
    defaultExportFormat: 'csv',
    includeHeaders: true,
    autoExport: false,
    
    // Data
    autoRefreshInterval: 30,
    lazyLoading: true,
    cacheData: true
  });

  const updateSettings = (newSettings) => {
    Object.assign(settings.value, newSettings);
    //localStorage.setItem('financeSettings', JSON.stringify(settings.value));
  };

  // Core workflow data - CLEANED UP naming
  const workflows = ref([]); // RENAMED: was workflowInstances - obvious!
  const workflowTemplates = ref([]);
  const workflowActions = ref([]);
  
  // REMOVED: workflows - redundant variable
  // REMOVED: inMemoryWorkflows - consolidated into workflows
  
  // Workflow states
  const WORKFLOW_STATES = {
    DRAFT: 'draft',
    ACTIVE: 'active',
    PENDING: 'pending',
    APPROVED: 'approved',
    REJECTED: 'rejected',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
    FAILED: 'failed'
  };

  const STEP_STATES = {
    PENDING: 'pending',
    ACTIVE: 'active',
    COMPLETED: 'completed',
    SKIPPED: 'skipped',
    FAILED: 'failed',
    REQUIRES_APPROVAL: 'requires_approval',
    APPROVED: 'approved',
    REJECTED: 'rejected'
  };

  const ACTION_TYPES = {
    // Core workflow actions
    DATA_COLLECTION: 'form_submission',
    APPROVAL: 'approval',
    VALIDATION: 'validation',
    INTEGRATION: 'integration',
    NOTIFICATION: 'notification',
    AUTOMATION: 'automation',
    REVIEW: 'review',
    DOCUMENT_UPLOAD: 'document_upload',
    BACKGROUND_CHECK: 'background_check',
    CONDITIONAL_ROUTING: 'conditional_routing',
    
    // Financial-specific actions
    FINANCIAL_VALIDATION: 'financial_validation',
    ACCOUNT_RECONCILIATION: 'account_reconciliation',
    TAX_CALCULATION: 'tax_calculation',
    BUDGET_APPROVAL: 'budget_approval',
    COMPLIANCE_CHECK: 'compliance_check',
    AUDIT_REVIEW: 'audit_review',
    PERIOD_CLOSE: 'period_close',
    ASSET_DEPRECIATION: 'asset_depreciation',
    CASH_FLOW_ANALYSIS: 'cash_flow_analysis',
    FINANCIAL_REPORTING: 'financial_reporting'
  };

  // Built-in workflow templates imported from JSON configuration
  const builtInTemplates = computed(() => {
    try {
      return readonly(getAllWorkflowTemplates());
    } catch (error) {
      console.warn('Failed to load workflow templates:', error);
      return readonly([]);
    }
  });

  // Computed properties - updated to use workflows only
  const activeWorkflows = computed(() => 
    workflows.value.filter(w => w.status === WORKFLOW_STATES.ACTIVE)
  );

  const pendingApprovals = computed(() => 
    workflows.value.filter(w => 
      w.currentStep && w.steps[w.currentStep - 1]?.status === STEP_STATES.REQUIRES_APPROVAL
    )
  );

  const completedWorkflows = computed(() => 
    workflows.value.filter(w => w.status === WORKFLOW_STATES.COMPLETED)
  );

  const draftWorkflows = computed(() => 
    workflows.value.filter(w => w.status === WORKFLOW_STATES.DRAFT)
  );

  // Core workflow methods - consolidated to work with workflows only
  
  // API function to fetch entity data structure
  async function fetchEntityDataStructure(entityName) {
    try {
      console.debug(`🔄 Fetching data structure for entity: ${entityName}`);
      
      // API endpoint to get entity schema/structure
      const apiEndpoint = `/api/entities/${entityName}/schema`;
      
      const response = await axios.get(apiEndpoint, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      if (response.status === 200 && response.data) {
        console.debug(`✅ Successfully fetched data structure for ${entityName}:`, response.data);
        return response.data;
      } else {
        throw new Error(`Invalid response from API: ${response.status}`);
      }
    } catch (error) {
      console.error(`❌ Failed to fetch data structure for entity ${entityName}:`, error);
      
      // Return fallback structure if API fails
      return {
        entity: entityName,
        fields: [],
        validation: {},
        relationships: [],
        error: `Failed to fetch schema: ${error.message}`
      };
    }
  }

  function createWorkflowInstance(templateId, data = {}) {
    const _template = builtInTemplates.value.find(t => t.id === templateId);
    
    console.debug('_template');
    console.debug(_template);

    if (!_template) {

      throw new Error(`Workflow template ${templateId} not found`);
    }

    const _workflow = {
      id: nextWorkflowId++,
      template_id: templateId,
      template: _template, // ← ADD THIS: Store the full template object
      name: _template.name,
      description: _template.description,
      status: WORKFLOW_STATES.DRAFT,
      currentStep: 1, // ← CHANGE FROM 0 TO 1
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      started_at: null,
      completed_at: null,
      created_by: data.created_by || 'system',
      data: data,
      steps: [],
      entityDataStructure: null, // Will be populated when workflow starts
      history: [
        {
          timestamp: new Date().toISOString(),
          action: 'workflow_created',
          user: data.created_by || 'system',
          details: `Workflow ${_template.name} created`
        }
      ],
      workflowNr: `WF-${Date.now().toString().slice(-6)}`,
      module: _template.module || 'general'
    };

    workflows.value.push(_workflow); // RENAMED: was workflowInstances
    return _workflow;
  }

  async function startWorkflow(workflowId) {
    const _workflow = workflows.value.find(w => w.id === workflowId); // RENAMED: was workflowInstances
    if (!_workflow) {
      throw new Error(`Workflow instance ${workflowId} not found`);
    }

    if (_workflow.status !== WORKFLOW_STATES.DRAFT) {
      throw new Error(`Workflow ${workflowId} cannot be started (current status: ${_workflow.status})`);
    }

    try {
      // Load the actual step definitions for this workflow
      const _template = builtInTemplates.value.find(t => t.id === _workflow.template_id);
      if (_template && _template.getSteps) {
        const _stepDefinitions = await _template.getSteps();
        
        // Fetch entity data structure from API
        if (_template.entity) {
          try {
            const entityDataStructure = await fetchEntityDataStructure(_template.entity);
            _workflow.entityDataStructure = entityDataStructure;
            console.debug(`✅ Loaded data structure for entity: ${_template.entity}`, entityDataStructure);
          } catch (error) {
            console.warn(`⚠️ Failed to fetch data structure for entity ${_template.entity}:`, error);
            // Continue without data structure - will use fallback
          }
        }
        
        // Validate the loaded workflow structure
        const { validateWorkflow } = useWorkflowValidation();
        const _workflowWithSteps = { ..._template, steps: _stepDefinitions };
        const validation = validateWorkflow(_workflowWithSteps);
        
        if (!validation.isValid) {
          console.warn('Workflow validation warnings:', validation.errors);
          // Continue with warnings, but log them for debugging
        }
        
        // Use the step definitions that were already loaded
        if (_stepDefinitions && _stepDefinitions.length > 0) {
          // Use actual template steps
          _workflow.steps = _stepDefinitions.map((step, index) => ({
            id: step.id || `step_${index + 1}`,
            name: step.name || `Step ${index + 1}`,
            description: step.description || '',
            type: step.type || 'form_submission',
            status: STEP_STATES.PENDING,
            started_at: null,
            completed_at: null,
            data: {},
            approvals: [],
            comments: [],
            // Copy all other step properties
            ...step
          }));
        } else {
          // Fallback - create basic steps
          const _stepsCount = 1;
          _workflow.steps = Array.from({ length: _stepsCount }, (_, index) => ({
            id: `step_${index + 1}`,
            name: `Step ${index + 1}`,
            status: STEP_STATES.PENDING,
            started_at: null,
            completed_at: null,
            data: {},
            approvals: [],
            comments: []
          }));
        }
      } else {
        // Simple fallback - just create basic steps
        const _stepsCount = _template?.steps || 1;
        _workflow.steps = Array.from({ length: _stepsCount }, (_, index) => ({
          id: `step_${index + 1}`,
          name: `Step ${index + 1}`,
          status: STEP_STATES.PENDING,
          started_at: null,
          completed_at: null,
          data: {},
          approvals: [],
          comments: []
        }));
      }

      // Start the workflow
      _workflow.status = WORKFLOW_STATES.ACTIVE;
      _workflow.started_at = new Date().toISOString();
      _workflow.updated_at = new Date().toISOString();
      
      // Start first step
      if (_workflow.steps.length > 0) {
        _workflow.steps[0].status = STEP_STATES.ACTIVE;
        _workflow.steps[0].started_at = new Date().toISOString();
        _workflow.currentStep = 1; // ← CHANGE FROM 0 TO 1
        console.debug('🚀 WORKFLOW STARTED: currentStep set to 1, first step activated');
      }

      _workflow.history.push({
        timestamp: new Date().toISOString(),
        action: 'workflow_started',
        user: 'system',
        details: 'Workflow started'
      });

      return _workflow;
    } catch (error) {
      console.error('Failed to start workflow:', error);
      throw error;
    }
  }

  function completeStep(workflowId, stepIndex, data = {}, user = 'system') {
    const _workflow = workflows.value.find(w => w.id === workflowId); // RENAMED: was workflowInstances
    if (!_workflow) {
      throw new Error(`Workflow instance ${workflowId} not found`);
    }

    const _step = _workflow.steps[stepIndex - 1]; // ← CHANGE: stepIndex - 1 for array access
    if (!_step) {
      throw new Error(`Step ${stepIndex} not found in workflow ${workflowId}`);
    }

    if (_step.status !== STEP_STATES.ACTIVE) {
      throw new Error(`Step ${stepIndex} is not active (current status: ${_step.status})`);
    }

    // Validate required fields if form_schema exists
    if (_step.form_schema && _step.form_schema.sections) {
      const validation = validateStepData(_step, data);
      if (!validation.isValid) {
        throw new Error(`Validation failed: ${validation.errors.join(', ')}`);
      }
    }

    // Update step
    _step.status = _step.approval_required ? STEP_STATES.REQUIRES_APPROVAL : STEP_STATES.COMPLETED;
    _step.data = { ..._step.data, ...data };
    _step.completed_at = new Date().toISOString();

    // Add to history
    _workflow.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_completed',
      user: user,
      step_index: stepIndex,
      step_name: _step.name,
      details: `Step "${_step.name}" completed`
    });

    // If step requires approval, don't advance yet
    if (_step.approval_required) {
      _workflow.status = WORKFLOW_STATES.PENDING;
      
      // Notify approvers
      notifyApprovers(_workflow, stepIndex);
      
      return _workflow;
    }

    // Move to next step or complete workflow
    return advanceCurrentStep(workflowId, user);
  }

  function approveStep(workflowId, stepIndex, approverId, comments = '') {
    const _workflow = workflows.value.find(w => w.id === workflowId); // RENAMED: was workflowInstances
    if (!_workflow) {
      throw new Error(`Workflow instance ${workflowId} not found`);
    }

    const _step = _workflow.steps[stepIndex];
    if (_step.status !== STEP_STATES.REQUIRES_APPROVAL) {
      throw new Error(`Step ${stepIndex} does not require approval`);
    }

    // Record approval
    _step.approvals.push({
      approver_id: approverId,
      approved_at: new Date().toISOString(),
      comments: comments
    });

    _step.status = STEP_STATES.APPROVED;
    _step.approved_at = new Date().toISOString();

    _workflow.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_approved',
      user: approverId,
      step_index: stepIndex,
      step_name: _step.name,
      details: `Step "${_step.name}" approved by ${approverId}. Comments: ${comments}`
    });

    // Move to next step
    return advanceCurrentStep(workflowId, approverId);
  }

  function rejectStep(workflowId, stepIndex, approverId, reason = '') {


    const _workflow = workflows.value.find(w => w.id === workflowId); // RENAMED: was workflowInstances
    if (!_workflow) {
      throw new Error(`Workflow instance ${workflowId} not found`);
    }

    const _step = _workflow.steps[stepIndex];
    if (!_step) {
      throw new Error(`Step ${stepIndex} not found in workflow ${workflowId}`);
    }

    if (_step.status !== STEP_STATES.REQUIRES_APPROVAL) {
      throw new Error(`Step ${stepIndex} does not require approval`);
    }

    _step.status = STEP_STATES.REJECTED;
    _step.rejected_at = new Date().toISOString();
    _step.rejection_reason = reason;

    _workflow.status = WORKFLOW_STATES.REJECTED;
    _workflow.updated_at = new Date().toISOString();

    _workflow.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_rejected',
      user: approverId,
      step_index: stepIndex,
      step_name: _step.name,
      details: `Step "${_step.name}" rejected by ${approverId}. Reason: ${reason}`
    });

    return _workflow;
  }




// ✅ KEEP ONLY THIS ONE - Enhanced version
function advanceCurrentStep(workflowId, user = 'system') {
  const workflow = workflows.value.find(w => w.id === workflowId)
  if (!workflow) {
    throw new Error(`Workflow ${workflowId} not found`)
  }

  // Mark current step as completed
  const currentStepIndex = workflow.currentStep - 1
  if (workflow.steps[currentStepIndex]) {
    workflow.steps[currentStepIndex].status = STEP_STATES.COMPLETED
    workflow.steps[currentStepIndex].completed_at = new Date().toISOString()
  }

  // Advance to next step
  workflow.currentStep += 1
  workflow.updated_at = new Date().toISOString()

  // Activate next step if it exists
  const nextStepIndex = workflow.currentStep - 1
  if (workflow.steps[nextStepIndex]) {
    workflow.steps[nextStepIndex].status = STEP_STATES.ACTIVE
    workflow.steps[nextStepIndex].started_at = new Date().toISOString()
  }

  return workflow
}



  // Helper functions
  function validateStepData(step, data) {
    const errors = [];
    
    if (step.validation_rules && step.validation_rules.required_fields) {
      step.validation_rules.required_fields.forEach(field => {
        if (!data[field] || data[field] === '') {
          errors.push(`Field "${field}" is required`);
        }
      });
    }

    return {
      isValid: errors.length === 0,
      errors: errors
    };
  }

  function notifyApprovers(instance, stepIndex) {
    const _step = instance.steps[stepIndex];
    
    // This would integrate with your notification system
    console.debug(`Notification: Step "${_step.name}" in workflow "${instance.name}" requires approval`);
    console.debug(`Approver roles: ${_step.approver_roles?.join(', ') || 'Not specified'}`);
    
    // Add notification to workflow
    instance.history.push({
      timestamp: new Date().toISOString(),
      action: 'approval_requested',
      user: 'system',
      step_index: stepIndex,
      details: `Approval requested for step "${_step.name}"`
    });
  }

  function executeCompletionActions(instance) {
    // Execute any automated actions on workflow completion
    console.debug(`Executing completion actions for workflow: ${instance.name}`);
    
    // This is where you'd integrate with other systems
    // - Create vendor record in ERP
    // - Send completion notifications
    // - Update external systems
    // - Generate reports
  }

  // Utility functions
  function getWorkflowById(workflowId) {
    return workflows.value.find(w => w.id === workflowId); // RENAMED: was workflowInstances
  }

  function getWorkflowsByTemplate(templateId) {
    return workflows.value.filter(w => w.template_id === templateId); // RENAMED: was workflowInstances
  }

  function getWorkflowsByStatus(status) {
    return workflows.value.filter(w => w.status === status); // RENAMED: was workflowInstances
  }

  function getWorkflowsByUser(userId) {
    return workflows.value.filter(w => w.created_by === userId); // RENAMED: was workflowInstances
  }

  // Utility function to get color classes for UI components
  function getColorClasses(colorType, element) {
    const colorMap = {
      blue: {
        icon: 'text-blue-600 dark:text-blue-400',
        bg: 'bg-blue-50 dark:bg-blue-900/20',
        value: 'text-gray-900 dark:text-white'
      },
      green: {
        icon: 'text-green-600 dark:text-green-400',
        bg: 'bg-green-50 dark:bg-green-900/20',
        value: 'text-green-600 dark:text-green-400'
      },
      orange: {
        icon: 'text-orange-600 dark:text-orange-400',
        bg: 'bg-orange-50 dark:bg-orange-900/20',
        value: 'text-orange-600 dark:text-orange-400'
      },
      purple: {
        icon: 'text-purple-600 dark:text-purple-400',
        bg: 'bg-purple-50 dark:bg-purple-900/20',
        value: 'text-purple-600 dark:text-purple-400'
      },
      indigo: {
        icon: 'text-indigo-600 dark:text-indigo-400',
        bg: 'bg-indigo-50 dark:bg-indigo-900/20',
        value: 'text-indigo-600 dark:text-indigo-400'
      },
      gray: {
        icon: 'text-gray-600 dark:text-gray-400',
        bg: 'bg-gray-50 dark:bg-gray-700',
        value: 'text-gray-900 dark:text-white'
      }
    }
    
    return colorMap[colorType]?.[element] || colorMap.gray[element]
  }

  // Export everything needed
  return {
    // Settings
    settings,
    updateSettings,
    
    // State - only core variables
    workflows, // RENAMED: was workflowInstances - obvious!
    workflowTemplates,
    workflowActions,
    builtInTemplates,
    
    // Constants
    WORKFLOW_STATES,
    STEP_STATES,
    ACTION_TYPES,
    
    // Computed - updated to use workflows only
    activeWorkflows,
    pendingApprovals,
    completedWorkflows,
    draftWorkflows,
    
    // Methods - only core methods
    createWorkflowInstance,
    startWorkflow,
    fetchEntityDataStructure,
    completeStep,
    approveStep,
    rejectStep,
    advanceCurrentStep,
    getWorkflowById,
    getWorkflowsByTemplate,
    getWorkflowsByStatus,
    getWorkflowsByUser,
    
    // Utility functions
    getColorClasses,
    
    // Validation
    validateWorkflow: useWorkflowValidation().validateWorkflow,
    validateStepTypes: useWorkflowValidation().validateStepTypes
  };
});