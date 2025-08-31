/**
 * Vendor Onboarding Workflow
 * @version 1.3.0
 * Complete vendor registration and validation process
 * @changes Step 1: shared_entity_selection for vendor selection, Step 2: form_submission for financial data
 */

export const steps = [
  {
    id: 'discovery',
    name: 'Vendor Discovery & Registration',
    description: 'Select existing vendor from vendors table',
    type: 'shared_entity_selection',
    entity_type: 'vendor',
    required: true,
    order: 1,

    entity_selection: {
      api_endpoint: '/api/vendors',
      search_fields: ['company_name', 'tax_id', 'primary_contact_email'],
      display_fields: ['company_name', 'business_type', 'primary_contact_name', 'primary_contact_email'],
      allow_create_new: false
    },
    approval_required: false,
    estimated_duration: '15 minutes'
  },
  {
    id: 'vendor_financial_info',
    name: 'Vendor Financial Information',
    description: 'Configure vendor-specific financial settings and parameters for finance_vendors table',
    type: 'form_submission',
    required: true,
    order: 2,

    form_schema: {
      sections: [
        {
          title: 'Financial Settings',
          fields: [
            { name: 'payment_terms', type: 'select', required: true, label: 'Payment Terms', options: ['Net 30', 'Net 45', 'Net 60', 'COD', 'Prepaid'] },
            { name: 'credit_limit', type: 'text', required: true, label: 'Credit Limit' },
            { name: 'tax_rate', type: 'text', required: true, label: 'Tax Rate (%)' },
            { name: 'discount_rate', type: 'text', required: false, label: 'Discount Rate (%)' }
          ]
        },
        {
          title: 'Banking Information',
          fields: [
            { name: 'bank_name', type: 'text', required: true, label: 'Bank Name' },
            { name: 'account_number', type: 'text', required: true, label: 'Account Number' },
            { name: 'routing_number', type: 'text', required: true, label: 'Routing Number' },
            { name: 'swift_code', type: 'text', required: false, label: 'SWIFT Code' }
          ]
        },
        {
          title: 'Contract Terms',
          fields: [
            { name: 'contract_start_date', type: 'date', required: true, label: 'Contract Start Date' },
            { name: 'contract_end_date', type: 'date', required: false, label: 'Contract End Date' },
            { name: 'auto_renewal', type: 'checkbox', required: false, label: 'Auto Renewal' },
            { name: 'termination_notice_days', type: 'text', required: true, label: 'Termination Notice (Days)' }
          ]
        }
      ]
    },
    validation_rules: {
      required_fields: ['payment_terms', 'credit_limit', 'tax_rate', 'bank_name', 'account_number', 'routing_number', 'contract_start_date', 'termination_notice_days'],
      custom_validations: [
        { field: 'tax_rate', rule: 'percentage_format' },
        { field: 'discount_rate', rule: 'percentage_format' }
      ]
    },
    approval_required: false,
    estimated_duration: '45 minutes'
  },
  {
    id: 'due_diligence',
    name: 'Due Diligence & Background Check',
    description: 'Verify vendor credentials, financial stability, and compliance',
    type: 'checklist',
    required: true,
    order: 3,

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
    type: 'approval',
    required: true,
    order: 4,

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
    type: 'form_submission',
    required: true,
    order: 5,

    form_schema: {
      sections: [
        {
          title: 'System Setup',
          fields: [
            { name: 'vendor_code', type: 'text', required: true, label: 'Vendor Code (Auto-generated)', readonly: true },
            { name: 'payment_method', type: 'select', required: true, label: 'Payment Method', options: ['ACH', 'Wire Transfer', 'Check', 'Credit Card'] },
            { name: 'purchase_categories', type: 'multi-select', required: true, label: 'Purchase Categories', options: ['Raw Materials', 'Office Supplies', 'IT Equipment', 'Professional Services', 'Maintenance'] },
            { name: 'access_level', type: 'select', required: true, label: 'System Access Level', options: ['Basic', 'Standard', 'Premium', 'Admin'] }
          ]
        }
      ]
    },
    approval_required: false,
    estimated_duration: '1-2 business days'
  },
  {
    id: 'integration_verification',
    name: 'Integration Verification',
    description: 'Verify that all system integration tasks have been completed',
    type: 'checklist',
    required: true,
    order: 6,

    form_schema: {
      sections: [
        {
          title: 'Integration Verification Checklist',
          fields: [
            { name: 'vendor_code_generated', type: 'checkbox', required: true, label: 'Vendor Code Generated' },
            { name: 'payment_method_setup', type: 'checkbox', required: true, label: 'Payment Method Configured' },
            { name: 'categories_assigned', type: 'checkbox', required: true, label: 'Purchase Categories Assigned' },
            { name: 'access_provided', type: 'checkbox', required: true, label: 'System Access Provided' }
          ]
        }
      ]
    },
    approval_required: false,
    estimated_duration: '30 minutes'
  },
  {
    id: 'final_approval',
    name: 'Final Approval & Activation',
    description: 'Final review and activation of vendor account',
    type: 'approval',
    required: true,
    order: 7,

    form_schema: {
      sections: [
        {
          title: 'Final Review',
          fields: [
            { name: 'all_steps_completed', type: 'checkbox', required: true, label: 'All Previous Steps Completed' },
            { name: 'final_approval_granted', type: 'checkbox', required: true, label: 'Final Approval Granted' },
            { name: 'vendor_activated', type: 'checkbox', required: true, label: 'Vendor Account Activated' },
            { name: 'activation_date', type: 'date', required: true, label: 'Activation Date' }
          ]
        },
        {
          title: 'Activation Notes',
          fields: [
            { name: 'activation_notes', type: 'textarea', required: false, label: 'Additional Notes' },
            { name: 'next_review_date', type: 'date', required: true, label: 'Next Review Date' }
          ]
        }
      ]
    },
    approval_required: true,
    approver_roles: ['procurement_director', 'finance_director'],
    estimated_duration: '1 business day'
  }
];

export default steps;
