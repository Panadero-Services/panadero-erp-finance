export const steps = [
  {
    id: 'discovery',
    name: 'Vendor Discovery & Registration',
    description: 'Initial vendor information collection and evaluation',
    type: 'form_submission',
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
        }
      ]
    },
    validation_rules: {
      required_fields: ['company_name', 'business_type', 'primary_contact_email'],
      custom_validations: [
        { field: 'primary_contact_email', rule: 'email_format' }
      ]
    },
    approval_required: false,
    estimated_duration: '30 minutes'
  },
  {
    id: 'due_diligence',
    name: 'Due Diligence & Background Check',
    description: 'Verify vendor credentials and compliance',
    type: 'background_check',
    required: true,
    order: 2,
    form_schema: {
      sections: [
        {
          title: 'Document Upload',
          fields: [
            { name: 'business_license', type: 'file', required: true, label: 'Business License', accept: '.pdf,.jpg,.png' },
            { name: 'insurance_certificate', type: 'file', required: true, label: 'Insurance Certificate', accept: '.pdf' },
            { name: 'financial_statements', type: 'file', required: true, label: 'Recent Financial Statements', accept: '.pdf' }
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
    order: 3,
    form_schema: {
      sections: [
        {
          title: 'Contract Terms',
          fields: [
            { name: 'payment_terms', type: 'select', required: true, label: 'Payment Terms', options: ['Net 30', 'Net 45', 'Net 60', 'COD'] },
            { name: 'service_level', type: 'textarea', required: true, label: 'Service Level Agreement' },
            { name: 'pricing_structure', type: 'textarea', required: true, label: 'Pricing Structure' }
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
    type: 'integration',
    required: true,
    order: 4,
    form_schema: {
      sections: [
        {
          title: 'System Setup',
          fields: [
            { name: 'vendor_code', type: 'text', required: true, label: 'Vendor Code (Auto-generated)', readonly: true },
            { name: 'payment_method', type: 'select', required: true, label: 'Payment Method', options: ['ACH', 'Wire Transfer', 'Check', 'Credit Card'] },
            { name: 'spending_limit', type: 'number', required: true, label: 'Initial Spending Limit' }
          ]
        }
      ]
    },
    approval_required: false,
    estimated_duration: '3-5 business days'
  },
  {
    id: 'relationship_management',
    name: 'Ongoing Relationship Management',
    description: 'Establish communication and performance monitoring',
    type: 'review',
    required: true,
    order: 5,
    form_schema: {
      sections: [
        {
          title: 'Performance Setup',
          fields: [
            { name: 'kpi_delivery_time', type: 'checkbox', required: true, label: 'On-time Delivery KPI Enabled' },
            { name: 'kpi_quality', type: 'checkbox', required: true, label: 'Product Quality KPI Enabled' },
            { name: 'review_frequency', type: 'select', required: true, label: 'Review Frequency', options: ['Monthly', 'Quarterly', 'Semi-Annually', 'Annually'] }
          ]
        }
      ]
    },
    approval_required: false,
    estimated_duration: '1-2 business days'
  }
];

export default steps;