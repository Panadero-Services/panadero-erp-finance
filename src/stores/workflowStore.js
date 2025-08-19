/**
 * Financial ERP Workflow Management Store
 * @version 1.0.8
 * @date 18-Aug-2025
 * @description Comprehensive workflow management system with 7 step types
 */
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
    // Core workflow actions
    DATA_COLLECTION: 'data_collection',
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

  // Built-in workflow templates (readonly to prevent mutations)
  const builtInTemplates = computed(() => readonly([
    {
      id: 'vendor-onboarding',
      name: 'Vendor Onboarding',
      description: 'Complete vendor registration and validation process',
      category: 'procurement',
      module: 'procurement',
      version: '1.0.8',
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
    },

    // ===== GENERAL LEDGER WORKFLOWS =====
    {
      id: 'gl-journal-entry-simple',
      name: 'Simple Journal Entry',
      description: 'Basic journal entry with standard debit/credit validation',
      category: 'general_ledger',
      module: 'gl',
      version: '1.0.8',
      steps: [
        {
          id: 'entry_creation',
          name: 'Journal Entry Creation',
          description: 'Create basic journal entry with account lines',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Entry Header',
                fields: [
                  { name: 'entry_date', type: 'date', required: true, label: 'Entry Date' },
                  { name: 'reference', type: 'text', required: true, label: 'Reference Number' },
                  { name: 'description', type: 'textarea', required: true, label: 'Description' },
                  { name: 'period', type: 'select', required: true, label: 'Accounting Period', options: ['Current', 'Previous', 'Next'] }
                ]
              },
              {
                title: 'Journal Lines',
                fields: [
                  { name: 'lines', type: 'dynamic_table', required: true, label: 'Account Lines', 
                    columns: [
                      { name: 'account', type: 'select', required: true, label: 'Account' },
                      { name: 'debit', type: 'number', required: false, label: 'Debit Amount' },
                      { name: 'credit', type: 'number', required: false, label: 'Credit Amount' },
                      { name: 'description', type: 'text', required: false, label: 'Line Description' }
                    ]
                  }
                ]
              }
            ]
          },
          validation_rules: {
            required_fields: ['entry_date', 'reference', 'description', 'lines'],
            custom_validations: [
              { rule: 'debit_credit_balance', message: 'Total debits must equal total credits' },
              { rule: 'at_least_two_lines', message: 'At least two account lines required' }
            ]
          },
          approval_required: false,
          estimated_duration: '15 minutes'
        },
        {
          id: 'validation',
          name: 'Entry Validation',
          description: 'Validate journal entry for accuracy and compliance',
          type: ACTION_TYPES.FINANCIAL_VALIDATION,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Validation Checklist',
                fields: [
                  { name: 'accounts_exist', type: 'checkbox', required: true, label: 'All accounts exist and are active' },
                  { name: 'period_open', type: 'checkbox', required: true, label: 'Accounting period is open' },
                  { name: 'amounts_valid', type: 'checkbox', required: true, label: 'All amounts are positive numbers' },
                  { name: 'description_clear', type: 'checkbox', required: true, label: 'Description is clear and complete' }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '5 minutes'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: false,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '30 minutes',
        escalation_rules: []
      }
    },

    // ===== ACCOUNTS PAYABLE WORKFLOWS =====
    {
      id: 'ap-vendor-invoice-approval',
      name: 'Vendor Invoice Approval',
      description: 'Multi-level approval process for vendor invoices',
      category: 'accounts_payable',
      module: 'ap',
      version: '1.0.8',
      steps: [
        {
          id: 'invoice_review',
          name: 'Invoice Review & Entry',
          description: 'Review vendor invoice and enter into system',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Invoice Details',
                fields: [
                  { name: 'vendor_id', type: 'select', required: true, label: 'Vendor' },
                  { name: 'invoice_number', type: 'text', required: true, label: 'Invoice Number' },
                  { name: 'invoice_date', type: 'date', required: true, label: 'Invoice Date' },
                  { name: 'due_date', type: 'date', required: true, label: 'Due Date' },
                  { name: 'total_amount', type: 'number', required: true, label: 'Total Amount' }
                ]
              },
              {
                title: 'Line Items',
                fields: [
                  { name: 'line_items', type: 'dynamic_table', required: true, label: 'Invoice Lines',
                    columns: [
                      { name: 'description', type: 'text', required: true, label: 'Description' },
                      { name: 'quantity', type: 'number', required: true, label: 'Quantity' },
                      { name: 'unit_price', type: 'number', required: true, label: 'Unit Price' },
                      { name: 'amount', type: 'number', required: true, label: 'Line Amount' }
                    ]
                  }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '20 minutes'
        },
        {
          id: 'manager_approval',
          name: 'Manager Approval',
          description: 'Department manager reviews and approves invoice',
          type: ACTION_TYPES.APPROVAL,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Approval Decision',
                fields: [
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['department_manager', 'finance_manager'],
          estimated_duration: '1-2 business days'
        },
        {
          id: 'finance_approval',
          name: 'Finance Approval',
          description: 'Finance team final approval for payment',
          type: ACTION_TYPES.APPROVAL,
          required: true,
          order: 3,
          form_schema: {
            sections: [
              {
                title: 'Finance Review',
                fields: [
                  { name: 'budget_available', type: 'checkbox', required: true, label: 'Budget available for this expense' },
                  { name: 'coding_correct', type: 'checkbox', required: true, label: 'Account coding is correct' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['finance_manager', 'controller'],
          estimated_duration: '1 business day'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '3-5 business days',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 2, action: 'notify_manager' },
          { condition: 'workflow_overdue_by_days', value: 5, action: 'escalate_to_director' }
        ]
      }
    },

    // ===== ACCOUNTS RECEIVABLE WORKFLOWS =====
    {
      id: 'ar-customer-invoice-creation',
      name: 'Customer Invoice Creation',
      description: 'Create and approve customer invoices with validation',
      category: 'accounts_receivable',
      module: 'ar',
      version: '1.0.8',
      steps: [
        {
          id: 'invoice_creation',
          name: 'Invoice Creation',
          description: 'Create customer invoice with line items',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Customer Information',
                fields: [
                  { name: 'customer_id', type: 'select', required: true, label: 'Customer' },
                  { name: 'invoice_date', type: 'date', required: true, label: 'Invoice Date' },
                  { name: 'due_date', type: 'date', required: true, label: 'Due Date' },
                  { name: 'payment_terms', type: 'select', required: true, label: 'Payment Terms', options: ['Net 30', 'Net 45', 'Net 60'] }
                ]
              },
              {
                title: 'Invoice Lines',
                fields: [
                  { name: 'line_items', type: 'dynamic_table', required: true, label: 'Invoice Lines',
                    columns: [
                      { name: 'description', type: 'text', required: true, label: 'Description' },
                      { name: 'quantity', type: 'number', required: true, label: 'Quantity' },
                      { name: 'unit_price', type: 'number', required: true, label: 'Unit Price' },
                      { name: 'tax_rate', type: 'select', required: false, label: 'Tax Rate' }
                    ]
                  }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '30 minutes'
        },
        {
          id: 'manager_review',
          name: 'Manager Review',
          description: 'Manager reviews invoice for accuracy',
          type: ACTION_TYPES.REVIEW,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Review Checklist',
                fields: [
                  { name: 'pricing_correct', type: 'checkbox', required: true, label: 'Pricing is correct' },
                  { name: 'terms_appropriate', type: 'checkbox', required: true, label: 'Payment terms are appropriate' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['sales_manager', 'finance_manager'],
          estimated_duration: '1 business day'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '1-2 business days',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 1, action: 'notify_manager' }
        ]
      }
    },

    // ===== CASH FLOW WORKFLOWS =====
    {
      id: 'cf-cash-forecast-approval',
      name: 'Cash Flow Forecast Approval',
      description: 'Monthly cash flow forecasting and approval process',
      category: 'cash_flow',
      module: 'cf',
      version: '1.0.8',
      steps: [
        {
          id: 'forecast_preparation',
          name: 'Forecast Preparation',
          description: 'Prepare cash flow forecast for the month',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Cash Inflows',
                fields: [
                  { name: 'customer_payments', type: 'number', required: true, label: 'Expected Customer Payments' },
                  { name: 'other_income', type: 'number', required: false, label: 'Other Income Sources' }
                ]
              },
              {
                title: 'Cash Outflows',
                fields: [
                  { name: 'vendor_payments', type: 'number', required: true, label: 'Vendor Payments Due' },
                  { name: 'payroll', type: 'number', required: true, label: 'Payroll Expenses' },
                  { name: 'operating_expenses', type: 'number', required: true, label: 'Operating Expenses' }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '2 hours'
        },
        {
          id: 'treasurer_review',
          name: 'Treasurer Review',
          description: 'Treasurer reviews forecast accuracy',
          type: ACTION_TYPES.REVIEW,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Review Decision',
                fields: [
                  { name: 'forecast_reasonable', type: 'checkbox', required: true, label: 'Forecast is reasonable' },
                  { name: 'assumptions_valid', type: 'checkbox', required: true, label: 'Assumptions are valid' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['treasurer', 'cfo'],
          estimated_duration: '1 business day'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '2-3 business days',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 2, action: 'notify_treasurer' }
        ]
      }
    },

    // ===== BUDGETING WORKFLOWS =====
    {
      id: 'budget-department-budget-approval',
      name: 'Department Budget Approval',
      description: 'Annual department budget creation and approval',
      category: 'budgeting',
      module: 'budget',
      version: '1.0.8',
      steps: [
        {
          id: 'budget_preparation',
          name: 'Budget Preparation',
          description: 'Department managers prepare annual budget',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Budget Categories',
                fields: [
                  { name: 'personnel', type: 'number', required: true, label: 'Personnel Costs' },
                  { name: 'operating', type: 'number', required: true, label: 'Operating Expenses' },
                  { name: 'capital', type: 'number', required: false, label: 'Capital Expenditures' },
                  { name: 'justification', type: 'textarea', required: true, label: 'Budget Justification' }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '1 week'
        },
        {
          id: 'finance_review',
          name: 'Finance Review',
          description: 'Finance team reviews budget for reasonableness',
          type: ACTION_TYPES.REVIEW,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Finance Assessment',
                fields: [
                  { name: 'budget_reasonable', type: 'checkbox', required: true, label: 'Budget is reasonable' },
                  { name: 'assumptions_valid', type: 'checkbox', required: true, label: 'Assumptions are valid' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['finance_manager', 'controller'],
          estimated_duration: '3-5 business days'
        },
        {
          id: 'executive_approval',
          name: 'Executive Approval',
          description: 'Final executive approval of department budget',
          type: ACTION_TYPES.APPROVAL,
          required: true,
          order: 3,
          form_schema: {
            sections: [
              {
                title: 'Executive Decision',
                fields: [
                  { name: 'strategic_alignment', type: 'checkbox', required: true, label: 'Budget aligns with strategy' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['cfo', 'ceo'],
          estimated_duration: '1 week'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '3-4 weeks',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 5, action: 'notify_finance_director' },
          { condition: 'workflow_overdue_by_days', value: 10, action: 'escalate_to_cfo' }
        ]
      }
    },

    // ===== FIXED ASSETS WORKFLOWS =====
    {
      id: 'fa-asset-acquisition-approval',
      name: 'Asset Acquisition Approval',
      description: 'Capital asset purchase approval and setup process',
      category: 'fixed_assets',
      module: 'fa',
      version: '1.0.8',
      steps: [
        {
          id: 'acquisition_request',
          name: 'Acquisition Request',
          description: 'Submit asset acquisition request with justification',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Asset Details',
                fields: [
                  { name: 'asset_name', type: 'text', required: true, label: 'Asset Name' },
                  { name: 'asset_type', type: 'select', required: true, label: 'Asset Type', options: ['Equipment', 'Vehicle', 'Building', 'Software', 'Other'] },
                  { name: 'estimated_cost', type: 'number', required: true, label: 'Estimated Cost' },
                  { name: 'useful_life', type: 'number', required: true, label: 'Useful Life (years)' },
                  { name: 'justification', type: 'textarea', required: true, label: 'Business Justification' }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '1 hour'
        },
        {
          id: 'department_approval',
          name: 'Department Approval',
          description: 'Department manager approves acquisition',
          type: ACTION_TYPES.APPROVAL,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Department Review',
                fields: [
                  { name: 'budget_available', type: 'checkbox', required: true, label: 'Budget available' },
                  { name: 'business_need', type: 'checkbox', required: true, label: 'Business need justified' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['department_manager'],
          estimated_duration: '2-3 business days'
        },
        {
          id: 'finance_approval',
          name: 'Finance Approval',
          description: 'Finance team reviews financial impact',
          type: ACTION_TYPES.APPROVAL,
          required: true,
          order: 3,
          form_schema: {
            sections: [
              {
                title: 'Financial Review',
                fields: [
                  { name: 'roi_analysis', type: 'checkbox', required: true, label: 'ROI analysis completed' },
                  { name: 'depreciation_impact', type: 'checkbox', required: true, label: 'Depreciation impact assessed' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['finance_manager', 'controller'],
          estimated_duration: '3-5 business days'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '1-2 weeks',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 3, action: 'notify_manager' },
          { condition: 'workflow_overdue_by_days', value: 7, action: 'escalate_to_director' }
        ]
      }
    },

    // ===== TAX MANAGEMENT WORKFLOWS =====
    {
      id: 'tax-quarterly-filing-preparation',
      name: 'Quarterly Tax Filing Preparation',
      description: 'Quarterly tax calculation and filing preparation',
      category: 'tax_management',
      module: 'tax',
      version: '1.0.8',
      steps: [
        {
          id: 'data_collection',
          name: 'Tax Data Collection',
          description: 'Collect financial data for tax calculations',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Income Data',
                fields: [
                  { name: 'gross_revenue', type: 'number', required: true, label: 'Gross Revenue' },
                  { name: 'deductions', type: 'number', required: true, label: 'Allowable Deductions' },
                  { name: 'net_income', type: 'number', required: true, label: 'Net Income' }
                ]
              },
              {
                title: 'Tax Information',
                fields: [
                  { name: 'tax_year', type: 'select', required: true, label: 'Tax Year', options: ['2024', '2025', '2026'] },
                  { name: 'quarter', type: 'select', required: true, label: 'Quarter', options: ['Q1', 'Q2', 'Q3', 'Q4'] }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '4 hours'
        },
        {
          id: 'tax_calculation',
          name: 'Tax Calculation',
          description: 'Calculate quarterly tax liability',
          type: ACTION_TYPES.TAX_CALCULATION,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                                 title: 'Calculation Results',
                 fields: [
                   { name: 'taxable_income', type: 'number', required: true, label: 'Taxable Income', readonly: true },
                   { name: 'tax_rate', type: 'number', required: true, label: 'Applicable Tax Rate', readonly: true },
                   { name: 'tax_liability', type: 'number', required: true, label: 'Tax Liability', readonly: true },
                   { name: 'estimated_payment', type: 'number', required: true, label: 'Estimated Payment Due' }
                 ]
               }
             ]
           },
          approval_required: false,
          estimated_duration: '2 hours'
        },
        {
          id: 'review_approval',
          name: 'Review & Approval',
          description: 'Tax professional reviews calculations',
          type: ACTION_TYPES.REVIEW,
          required: true,
          order: 3,
          form_schema: {
            sections: [
              {
                title: 'Professional Review',
                fields: [
                  { name: 'calculations_accurate', type: 'checkbox', required: true, label: 'Calculations are accurate' },
                  { name: 'deductions_valid', type: 'checkbox', required: true, label: 'Deductions are valid' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['tax_accountant', 'cpa'],
          estimated_duration: '1-2 business days'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '1 week',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 2, action: 'notify_tax_manager' }
        ]
      }
    },

    // ===== COMPLIANCE & AUDIT WORKFLOWS =====
    {
      id: 'compliance-monthly-reconciliation',
      name: 'Monthly Account Reconciliation',
      description: 'Monthly bank and account reconciliation process',
      category: 'compliance_audit',
      module: 'compliance',
      version: '1.0.8',
      steps: [
        {
          id: 'reconciliation_preparation',
          name: 'Reconciliation Preparation',
          description: 'Prepare bank statements and account balances',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Bank Information',
                fields: [
                  { name: 'bank_statement_date', type: 'date', required: true, label: 'Bank Statement Date' },
                  { name: 'ending_balance', type: 'number', required: true, label: 'Bank Ending Balance' },
                  { name: 'account_number', type: 'text', required: true, label: 'Account Number' }
                ]
              },
              {
                title: 'GL Balances',
                fields: [
                  { name: 'gl_ending_balance', type: 'number', required: true, label: 'GL Ending Balance' },
                  { name: 'outstanding_items', type: 'textarea', required: false, label: 'Outstanding Items' }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '2 hours'
        },
        {
          id: 'reconciliation_review',
          name: 'Reconciliation Review',
          description: 'Review reconciliation for accuracy',
          type: ACTION_TYPES.ACCOUNT_RECONCILIATION,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Review Checklist',
                fields: [
                  { name: 'balances_match', type: 'checkbox', required: true, label: 'Balances match after adjustments' },
                  { name: 'outstanding_items_documented', type: 'checkbox', required: true, label: 'Outstanding items documented' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['accountant', 'finance_manager'],
          estimated_duration: '1 business day'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '3-5 business days',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 2, action: 'notify_finance_manager' }
        ]
      }
    },

    // ===== REPORTING WORKFLOWS =====
    {
      id: 'report-financial-statements',
      name: 'Financial Statement Preparation',
      description: 'Monthly/quarterly financial statement preparation process',
      category: 'financial_reporting',
      module: 'reporting',
      version: '1.0.8',
      steps: [
        {
          id: 'data_preparation',
          name: 'Data Preparation',
          description: 'Prepare financial data for statements',
          type: ACTION_TYPES.DATA_COLLECTION,
          required: true,
          order: 1,
          form_schema: {
            sections: [
              {
                title: 'Statement Period',
                fields: [
                  { name: 'statement_period', type: 'select', required: true, label: 'Statement Period', options: ['Monthly', 'Quarterly', 'Annual'] },
                  { name: 'period_end_date', type: 'date', required: true, label: 'Period End Date' },
                  { name: 'comparative_period', type: 'select', required: true, label: 'Comparative Period', options: ['Previous Month', 'Previous Quarter', 'Previous Year'] }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '1 day'
        },
        {
          id: 'statement_preparation',
          name: 'Statement Preparation',
          description: 'Prepare income statement, balance sheet, and cash flow',
          type: ACTION_TYPES.FINANCIAL_REPORTING,
          required: true,
          order: 2,
          form_schema: {
            sections: [
              {
                title: 'Statement Status',
                fields: [
                  { name: 'income_statement_complete', type: 'checkbox', required: true, label: 'Income Statement Complete' },
                  { name: 'balance_sheet_complete', type: 'checkbox', required: true, label: 'Balance Sheet Complete' },
                  { name: 'cash_flow_complete', type: 'checkbox', required: true, label: 'Cash Flow Statement Complete' },
                  { name: 'notes_complete', type: 'checkbox', required: true, label: 'Notes to Financial Statements Complete' }
                ]
              }
            ]
          },
          approval_required: false,
          estimated_duration: '2-3 days'
        },
        {
          id: 'management_review',
          name: 'Management Review',
          description: 'Management reviews financial statements',
          type: ACTION_TYPES.REVIEW,
          required: true,
          order: 3,
          form_schema: {
            sections: [
              {
                title: 'Management Review',
                fields: [
                  { name: 'statements_accurate', type: 'checkbox', required: true, label: 'Statements are accurate' },
                  { name: 'variances_explained', type: 'checkbox', required: true, label: 'Variances are explained' },
                  { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Reject'] },
                  { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
                ]
              }
            ]
          },
          approval_required: true,
          approver_roles: ['finance_manager', 'controller', 'cfo'],
          estimated_duration: '2-3 business days'
        }
      ],
      notification_settings: {
        step_completion: true,
        approval_required: true,
        workflow_completion: true
      },
      sla_settings: {
        total_duration: '1-2 weeks',
        escalation_rules: [
          { condition: 'step_overdue_by_days', value: 3, action: 'notify_finance_manager' },
          { condition: 'workflow_overdue_by_days', value: 7, action: 'escalate_to_controller' }
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
