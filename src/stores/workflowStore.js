import { defineStore } from 'pinia';
import { ref, computed, readonly } from 'vue';

export const useWorkflowStore = defineStore('workflow', () => {
  // Core workflow data
  const workflows = ref([]);
  const workflowInstances = ref([]);
  const workflowTemplates = ref([]);
  const workflowActions = ref([]);
  
  // Current active workflow
  const activeWorkflow = ref(null);
  const currentStep = ref(0);
  
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
    DATA_COLLECTION: 'data_collection',
    APPROVAL: 'approval',
    VALIDATION: 'validation',
    INTEGRATION: 'integration',
    NOTIFICATION: 'notification',
    AUTOMATION: 'automation',
    REVIEW: 'review',
    DOCUMENT_UPLOAD: 'document_upload',
    BACKGROUND_CHECK: 'background_check',
    CONDITIONAL_ROUTING: 'conditional_routing'
  };

  // Built-in workflow templates (readonly to prevent mutations)
  const builtInTemplates = computed(() => readonly([
    {
      id: 'vendor-onboarding',
      name: 'Vendor Onboarding',
      description: 'Complete vendor registration and validation process',
      category: 'procurement',
      version: '1.0.0',
      steps: [
        {
          id: 'discovery',
          name: 'Vendor Discovery & Registration',
          description: 'Initial vendor information collection and evaluation',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Basic Information',
                fields: [
                  { name: 'company_name', type: 'text', required: true, label: 'Company Name' },
                  { name: 'business_type', type: 'select', required: true, label: 'Business Type', options: ['Corporation', 'LLC', 'Partnership', 'Sole Proprietorship'] },
                  { name: 'industry', type: 'select', required: true, label: 'Industry', options: ['Manufacturing', 'Services', 'Technology', 'Healthcare', 'Other'] },
                  { name: 'website', type: 'url', required: false, label: 'Website' }
                ]
              },
              {
                title: 'Contact Information',
                fields: [
                  { name: 'primary_contact_name', type: 'text', required: true, label: 'Primary Contact Name' },
                  { name: 'primary_contact_email', type: 'email', required: true, label: 'Primary Contact Email' },
                  { name: 'primary_contact_phone', type: 'tel', required: true, label: 'Primary Contact Phone' },
                  { name: 'business_address', type: 'textarea', required: true, label: 'Business Address' }
                ]
              },
              {
                title: 'Financial Information',
                fields: [
                  { name: 'tax_id', type: 'text', required: true, label: 'Tax ID / EIN' },
                  { name: 'bank_name', type: 'text', required: true, label: 'Bank Name' },
                  { name: 'account_number', type: 'text', required: true, label: 'Account Number' },
                  { name: 'routing_number', type: 'text', required: true, label: 'Routing Number' }
                ]
              }
            ]
          },
          validation_rules: {
            required_fields: ['company_name', 'business_type', 'primary_contact_email', 'tax_id'],
            custom_validations: [
              { field: 'primary_contact_email', rule: 'email_format' },
              { field: 'tax_id', rule: 'tax_id_format' }
            ]
          },
          approval_required: false,
          estimated_duration: '30 minutes'
        },
        {
          id: 'due_diligence',
          name: 'Due Diligence & Background Check',
          description: 'Verify vendor credentials, financial stability, and compliance',
          type: ACTION_TYPES.BACKGROUND_CHECK,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Document Upload',
                fields: [
                  { name: 'business_license', type: 'file', required: true, label: 'Business License', accept: '.pdf,.jpg,.png' },
                  { name: 'insurance_certificate', type: 'file', required: true, label: 'Insurance Certificate', accept: '.pdf' },
                  { name: 'financial_statements', type: 'file', required: true, label: 'Recent Financial Statements', accept: '.pdf' },
                  { name: 'references', type: 'file', required: false, label: 'Business References', accept: '.pdf,.doc,.docx' }
                ]
              },
              {
                title: 'Verification Checklist',
                fields: [
                  { name: 'business_license_verified', type: 'checkbox', required: true, label: 'Business License Verified' },
                  { name: 'insurance_active', type: 'checkbox', required: true, label: 'Insurance Coverage Active' },
                  { name: 'financial_stability_check', type: 'select', required: true, label: 'Financial Stability', options: ['Excellent', 'Good', 'Fair', 'Poor'] },
                  { name: 'compliance_check', type: 'checkbox', required: true, label: 'Regulatory Compliance Verified' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['procurement_manager', 'finance_manager'],
          estimated_duration: '2-3 business days'
        },
        {
          id: 'contract_negotiation',
          name: 'Contract Negotiation & Agreement',
          description: 'Negotiate terms and execute binding agreement',
          type: ACTION_TYPES.APPROVAL,
          required: true,
          order: 3,
          form_schema: {
            sections: [
              {
                title: 'Contract Terms',
                fields: [
                  { name: 'payment_terms', type: 'select', required: true, label: 'Payment Terms', options: ['Net 30', 'Net 45', 'Net 60', 'COD', 'Prepaid'] },
                  { name: 'delivery_terms', type: 'select', required: true, label: 'Delivery Terms', options: ['FOB Origin', 'FOB Destination', 'CIF', 'DDP'] },
                  { name: 'service_level', type: 'textarea', required: true, label: 'Service Level Agreement' },
                  { name: 'pricing_structure', type: 'textarea', required: true, label: 'Pricing Structure' }
                ]
              },
              {
                title: 'Legal Review',
                fields: [
                  { name: 'contract_reviewed', type: 'checkbox', required: true, label: 'Contract Reviewed by Legal' },
                  { name: 'terms_agreed', type: 'checkbox', required: true, label: 'Terms Agreed by Both Parties' },
                  { name: 'contract_signed', type: 'checkbox', required: true, label: 'Contract Signed' },
                  { name: 'contract_file', type: 'file', required: true, label: 'Signed Contract', accept: '.pdf' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['legal_counsel', 'procurement_director'],
          estimated_duration: '1-2 weeks'
        },
        {
          id: 'integration_setup',
          name: 'System Integration & Setup',
          description: 'Integrate vendor into systems and provide access',
          type: ACTION_TYPES.INTEGRATION,
          required: true,
          order: 4,
          form_schema: {
            sections: [
              {
                title: 'System Setup',
                fields: [
                  { name: 'vendor_code', type: 'text', required: true, label: 'Vendor Code (Auto-generated)', readonly: true },
                  { name: 'payment_method', type: 'select', required: true, label: 'Payment Method', options: ['ACH', 'Wire Transfer', 'Check', 'Credit Card'] },
                  { name: 'purchase_categories', type: 'multi-select', required: true, label: 'Purchase Categories', options: ['Raw Materials', 'Office Supplies', 'IT Equipment', 'Professional Services', 'Maintenance'] },
                  { name: 'spending_limit', type: 'number', required: true, label: 'Initial Spending Limit' }
                ]
              },
              {
                title: 'Access & Training',
                fields: [
                  { name: 'portal_access_granted', type: 'checkbox', required: true, label: 'Vendor Portal Access Granted' },
                  { name: 'training_completed', type: 'checkbox', required: true, label: 'System Training Completed' },
                  { name: 'test_transaction', type: 'checkbox', required: true, label: 'Test Transaction Processed' },
                  { name: 'go_live_date', type: 'date', required: true, label: 'Go-Live Date' }
                ]
              }
            ]
          },
          approval_required: false,
          automated_actions: [
            { action: 'generate_vendor_code', trigger: 'step_start' },
            { action: 'create_vendor_account', trigger: 'form_complete' },
            { action: 'send_welcome_email', trigger: 'step_complete' }
          ],
          estimated_duration: '3-5 business days'
        },
        {
          id: 'relationship_management',
          name: 'Ongoing Relationship Management',
          description: 'Establish communication and performance monitoring',
          type: ACTION_TYPES.REVIEW,
          required: true,
          order: 5,
          form_schema: {
            sections: [
              {
                title: 'Performance Setup',
                fields: [
                  { name: 'kpi_delivery_time', type: 'checkbox', required: true, label: 'On-time Delivery KPI Enabled' },
                  { name: 'kpi_quality', type: 'checkbox', required: true, label: 'Product Quality KPI Enabled' },
                  { name: 'kpi_service', type: 'checkbox', required: true, label: 'Service Level KPI Enabled' },
                  { name: 'review_frequency', type: 'select', required: true, label: 'Review Frequency', options: ['Monthly', 'Quarterly', 'Semi-Annually', 'Annually'] }
                ]
              },
              {
                title: 'Communication Setup',
                fields: [
                  { name: 'primary_liaison', type: 'text', required: true, label: 'Primary Company Liaison' },
                  { name: 'escalation_contact', type: 'text', required: true, label: 'Escalation Contact' },
                  { name: 'communication_preferences', type: 'multi-select', required: true, label: 'Communication Channels', options: ['Email', 'Phone', 'Video Calls', 'In-Person Meetings'] },
                  { name: 'first_review_date', type: 'date', required: true, label: 'First Performance Review Date' }
                ]
              }
            ]
          },
          approval_required: false,
          automated_actions: [
            { action: 'schedule_first_review', trigger: 'step_complete' },
            { action: 'add_to_vendor_directory', trigger: 'step_complete' }
          ],
          estimated_duration: '1-2 business days'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true,
        reminder_frequency: 'daily'
      },
      sla_settings: {
        total_duration: '4-6 weeks',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 3, action: 'notify_manager' },
          { condition: 'workflow_overdue_by_days', value: 7, action: 'escalate_to_director' }
        ]
      }
    }
  ]));

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
      steps: template.steps.map(step => ({
        ...step,
        status: STEP_STATES.PENDING,
        started_at: null,
        completed_at: null,
        data: {},
        approvals: [],
        comments: []
      })),
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

  function startWorkflow(instanceId) {
    const instance = workflowInstances.value.find(w => w.id === instanceId);
    if (!instance) {
      throw new Error(`Workflow instance ${instanceId} not found`);
    }

    if (instance.status !== WORKFLOW_STATES.DRAFT) {
      throw new Error(`Workflow ${instanceId} cannot be started (current status: ${instance.status})`);
    }

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

  // Export everything needed
  return {
    // State
    workflows,
    workflowInstances,
    workflowTemplates,
    workflowActions,
    activeWorkflow,
    currentStep,
    builtInTemplates,
    
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
    getWorkflowsByUser
  };
});
