/**
 * Financial ERP Workflow Management Store
 * @version 1.0.9
 * @date 19-Aug-2025
 * @description Comprehensive workflow management system with 7 step types and state management
 */
import { defineStore } from 'pinia';
import { ref, computed, readonly } from 'vue';
import axios from 'axios';
import { getAllWorkflowTemplates } from '../data/workflowTemplatesConfig.js';

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

  // Core workflow data
  const workflows = ref([]);
  const workflowInstances = ref([]);
  const workflowTemplates = ref([]);
  const workflowActions = ref([]);
  
  // Current active workflow
  const activeWorkflow = ref(null);
  const currentStep = ref(0);
  
  // Workflow execution state (moved from financeStore)
  const workflowExecution = ref({
    currentStep: 0,
    stepData: {},
    sharedEntities: {},
    selectedVendor: null,
    isLoading: false,
    error: null
  });
  
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

  // Computed properties
  const activeWorkflows = computed(() => 
    workflowInstances.value.filter(w => w.status === WORKFLOW_STATES.ACTIVE)
  );

  const pendingApprovals = computed(() => 
    workflowInstances.value.filter(w => 
      w.current_step && w.steps[w.current_step]?.status === STEP_STATES.REQUIRES_APPROVAL
    )
  );

  const completedWorkflows = computed(() => 
    workflowInstances.value.filter(w => w.status === WORKFLOW_STATES.COMPLETED)
  );

  // Core workflow methods
  function createWorkflowInstance(templateId, data = {}) {
    const template = builtInTemplates.value.find(t => t.id === templateId);
    if (!template) {
      throw new Error(`Workflow template ${templateId} not found`);
    }

    const instance = {
      id: `${templateId}-${Date.now()}`,
      template_id: templateId,
      name: template.name,
      description: template.description,
      status: WORKFLOW_STATES.DRAFT,
      current_step: 0,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      created_by: data.created_by || 'system',
      data: data,
      // Initialize with empty steps array - will be populated when workflow starts
      steps: [],
      history: [
        {
          timestamp: new Date().toISOString(),
          action: 'workflow_created',
          user: data.created_by || 'system',
          details: `Workflow ${template.name} created`
        }
      ]
    };

    workflowInstances.value.push(instance);
    return instance;
  }

  async function startWorkflow(instanceId) {
    const instance = workflowInstances.value.find(w => w.id === instanceId);
    if (!instance) {
      throw new Error(`Workflow instance ${instanceId} not found`);
    }

    if (instance.status !== WORKFLOW_STATES.DRAFT) {
      throw new Error(`Workflow ${instanceId} cannot be started (current status: ${instance.status})`);
    }

    try {
      // Load the actual step definitions for this workflow
      const template = builtInTemplates.value.find(t => t.id === instance.template_id);
      if (template && template.getSteps) {
        const stepDefinitions = await template.getSteps();
        
        // Initialize steps with the loaded definitions
        instance.steps = stepDefinitions.map(step => ({
          ...step,
          status: STEP_STATES.PENDING,
          started_at: null,
          completed_at: null,
          data: {},
          approvals: [],
          comments: []
        }));
      } else {
        // Fallback: create placeholder steps based on the steps count
        const stepsCount = template?.steps || 1;
        instance.steps = Array.from({ length: stepsCount }, (_, index) => ({
          id: `step_${index + 1}`,
          name: `Step ${index + 1}`,
          description: `Step ${index + 1} of ${stepsCount}`,
          type: 'form_submission',
          required: true,
          order: index + 1,
          status: STEP_STATES.PENDING,
          started_at: null,
          completed_at: null,
          data: {},
          approvals: [],
          comments: []
        }));
      }

      // Start the workflow
      instance.status = WORKFLOW_STATES.ACTIVE;
      instance.started_at = new Date().toISOString();
      instance.updated_at = new Date().toISOString();
      
      // Start first step
      if (instance.steps.length > 0) {
        instance.steps[0].status = STEP_STATES.ACTIVE;
        instance.steps[0].started_at = new Date().toISOString();
        instance.current_step = 0;
      }

      instance.history.push({
        timestamp: new Date().toISOString(),
        action: 'workflow_started',
        user: 'system',
        details: 'Workflow started'
      });

      return instance;
    } catch (error) {
      console.error('Failed to start workflow:', error);
      throw error;
    }
  }

  function completeStep(instanceId, stepIndex, data = {}, user = 'system') {
    const instance = workflowInstances.value.find(w => w.id === instanceId);
    if (!instance) {
      throw new Error(`Workflow instance ${instanceId} not found`);
    }

    const step = instance.steps[stepIndex];
    if (!step) {
      throw new Error(`Step ${stepIndex} not found in workflow ${instanceId}`);
    }

    if (step.status !== STEP_STATES.ACTIVE) {
      throw new Error(`Step ${stepIndex} is not active (current status: ${step.status})`);
    }

    // Validate required fields if form_schema exists
    if (step.form_schema && step.form_schema.sections) {
      const validation = validateStepData(step, data);
      if (!validation.isValid) {
        throw new Error(`Validation failed: ${validation.errors.join(', ')}`);
      }
    }

    // Update step
    step.status = step.approval_required ? STEP_STATES.REQUIRES_APPROVAL : STEP_STATES.COMPLETED;
    step.data = { ...step.data, ...data };
    step.completed_at = new Date().toISOString();

    // Add to history
    instance.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_completed',
      user: user,
      step_index: stepIndex,
      step_name: step.name,
      details: `Step "${step.name}" completed`
    });

    // If step requires approval, don't advance yet
    if (step.approval_required) {
      instance.status = WORKFLOW_STATES.PENDING;
      
      // Notify approvers
      notifyApprovers(instance, stepIndex);
      
      return instance;
    }

    // Move to next step or complete workflow
    return advanceWorkflow(instanceId, user);
  }

  function approveStep(instanceId, stepIndex, approverId, comments = '') {
    const instance = workflowInstances.value.find(w => w.id === instanceId);
    if (!instance) {
      throw new Error(`Workflow instance ${instanceId} not found`);
    }

    const step = instance.steps[stepIndex];
    if (step.status !== STEP_STATES.REQUIRES_APPROVAL) {
      throw new Error(`Step ${stepIndex} does not require approval`);
    }

    // Record approval
    step.approvals.push({
      approver_id: approverId,
      approved_at: new Date().toISOString(),
      comments: comments
    });

    step.status = STEP_STATES.APPROVED;
    step.approved_at = new Date().toISOString();

    instance.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_approved',
      user: approverId,
      step_index: stepIndex,
      step_name: step.name,
      details: `Step "${step.name}" approved by ${approverId}. Comments: ${comments}`
    });

    // Move to next step
    return advanceWorkflow(instanceId, approverId);
  }

  function rejectStep(instanceId, stepIndex, approverId, reason = '') {
    const instance = workflowInstances.value.find(w => w.id === instanceId);
    if (!instance) {
      throw new Error(`Workflow instance ${instanceId} not found`);
    }

    const step = instance.steps[stepIndex];
    if (step.status !== STEP_STATES.REQUIRES_APPROVAL) {
      throw new Error(`Step ${stepIndex} does not require approval`);
    }

    step.status = STEP_STATES.REJECTED;
    step.rejected_at = new Date().toISOString();
    step.rejection_reason = reason;

    instance.status = WORKFLOW_STATES.REJECTED;
    instance.updated_at = new Date().toISOString();

    instance.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_rejected',
      user: approverId,
      step_index: stepIndex,
      step_name: step.name,
      details: `Step "${step.name}" rejected by ${approverId}. Reason: ${reason}`
    });

    return instance;
  }

  function advanceWorkflow(instanceId, user = 'system') {
    const instance = workflowInstances.value.find(w => w.id === instanceId);
    if (!instance) {
      throw new Error(`Workflow instance ${instanceId} not found`);
    }

    const nextStepIndex = instance.current_step + 1;
    
    if (nextStepIndex >= instance.steps.length) {
      // Workflow complete
      instance.status = WORKFLOW_STATES.COMPLETED;
      instance.completed_at = new Date().toISOString();
      instance.updated_at = new Date().toISOString();

      instance.history.push({
        timestamp: new Date().toISOString(),
        action: 'workflow_completed',
        user: user,
        details: 'Workflow completed successfully'
      });

      // Execute completion actions
      executeCompletionActions(instance);
      
      return instance;
    }

    // Start next step
    const nextStep = instance.steps[nextStepIndex];
    nextStep.status = STEP_STATES.ACTIVE;
    nextStep.started_at = new Date().toISOString();
    
    instance.current_step = nextStepIndex;
    instance.status = WORKFLOW_STATES.ACTIVE;
    instance.updated_at = new Date().toISOString();

    instance.history.push({
      timestamp: new Date().toISOString(),
      action: 'step_started',
      user: user,
      step_index: nextStepIndex,
      step_name: nextStep.name,
      details: `Step "${nextStep.name}" started`
    });

    return instance;
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
    const step = instance.steps[stepIndex];
    
    // This would integrate with your notification system
    console.log(`Notification: Step "${step.name}" in workflow "${instance.name}" requires approval`);
    console.log(`Approver roles: ${step.approver_roles?.join(', ') || 'Not specified'}`);
    
    // Add notification to workflow
    instance.history.push({
      timestamp: new Date().toISOString(),
      action: 'approval_requested',
      user: 'system',
      step_index: stepIndex,
      details: `Approval requested for step "${step.name}"`
    });
  }

  function executeCompletionActions(instance) {
    // Execute any automated actions on workflow completion
    console.log(`Executing completion actions for workflow: ${instance.name}`);
    
    // This is where you'd integrate with other systems
    // - Create vendor record in ERP
    // - Send completion notifications
    // - Update external systems
    // - Generate reports
  }

  // Utility functions
  function getWorkflowById(instanceId) {
    return workflowInstances.value.find(w => w.id === instanceId);
  }

  function getWorkflowsByTemplate(templateId) {
    return workflowInstances.value.filter(w => w.template_id === templateId);
  }

  function getWorkflowsByStatus(status) {
    return workflowInstances.value.filter(w => w.status === status);
  }

  function getWorkflowsByUser(userId) {
    return workflowInstances.value.filter(w => w.created_by === userId);
  }

  // Workflow execution methods (moved from financeStore)
  async function fetchSharedEntity(entityType, params = {}) {
    const entityConfig = {
      vendors: {
        endpoint: '/api/table/vendors',
        displayField: 'name',
        identifierField: 'id'
      }
      // Add more entities here following same structure
    }
    
    const config = entityConfig[entityType]
    if (!config) throw new Error(`Unknown entity type: ${entityType}`)
    
    try {
      workflowExecution.value.isLoading = true
      workflowExecution.value.error = null
      
      const response = await axios.get(config.endpoint, {
        params,
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      
      workflowExecution.value.sharedEntities[entityType] = response.data.data || response.data
      return workflowExecution.value.sharedEntities[entityType]
    } catch (error) {
      workflowExecution.value.error = error.response?.data?.message || error.message
      throw error
    } finally {
      workflowExecution.value.isLoading = false
    }
  }

  // Workflow step management
  function setCurrentStep(stepIndex) {
    workflowExecution.value.currentStep = stepIndex
  }

  function updateStepData(stepId, data) {
    workflowExecution.value.stepData[stepId] = { ...workflowExecution.value.stepData[stepId], ...data }
  }

  function selectVendor(vendor) {
    workflowExecution.value.selectedVendor = vendor
    workflowExecution.value.stepData.vendor_selection = { vendor_id: vendor.id, vendor_name: vendor.name }
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
    
    // State
    workflows,
    workflowInstances,
    workflowTemplates,
    workflowActions,
    activeWorkflow,
    currentStep,
    builtInTemplates,
    
    // Workflow execution state (moved from financeStore)
    workflowExecution,
    
    // Constants
    WORKFLOW_STATES,
    STEP_STATES,
    ACTION_TYPES,
    
    // Computed
    activeWorkflows,
    pendingApprovals,
    completedWorkflows,
    
    // Methods
    createWorkflowInstance,
    startWorkflow,
    completeStep,
    approveStep,
    rejectStep,
    advanceWorkflow,
    getWorkflowById,
    getWorkflowsByTemplate,
    getWorkflowsByStatus,
    getWorkflowsByUser,
    
    // Workflow execution methods (moved from financeStore)
    fetchSharedEntity,
    setCurrentStep,
    updateStepData,
    selectVendor,
    
    // Utility functions
    getColorClasses
  };
});
