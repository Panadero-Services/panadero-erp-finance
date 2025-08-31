export const steps = [
    {
      id: 'contract_review',
      name: 'Contract Review',
      description: 'Review existing contract terms and performance',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Contract Details',
            fields: [
              { name: 'contract_id', type: 'text', required: true, label: 'Contract ID' },
              { name: 'counterparty', type: 'text', required: true, label: 'Counterparty' },
              { name: 'current_expiry', type: 'date', required: true, label: 'Current Expiry Date' },
              { name: 'proposed_renewal_term', type: 'select', required: true, label: 'Proposed Renewal Term', options: ['1 Year', '2 Years', '3 Years', '5 Years'] },
              { name: 'current_value', type: 'number', required: true, label: 'Current Contract Value' }
            ]
          },
          {
            title: 'Performance Assessment',
            fields: [
              { name: 'performance_rating', type: 'select', required: true, label: 'Performance Rating', options: ['Excellent', 'Good', 'Fair', 'Poor'] },
              { name: 'service_quality', type: 'select', required: true, label: 'Service Quality', options: ['Excellent', 'Good', 'Fair', 'Poor'] },
              { name: 'compliance_record', type: 'select', required: true, label: 'Compliance Record', options: ['Excellent', 'Good', 'Fair', 'Poor'] },
              { name: 'performance_notes', type: 'textarea', required: false, label: 'Performance Notes' }
            ]
          }
        ]
      },
      validation_rules: {
        required_fields: ['contract_id', 'counterparty', 'current_expiry', 'proposed_renewal_term', 'current_value'],
        custom_validations: [
          { rule: 'positive_value', message: 'Contract value must be positive' },
          { rule: 'future_expiry', message: 'Current expiry must be in the future' }
        ]
      },
      approval_required: false,
      estimated_duration: '2-3 days'
    },
    {
      id: 'renewal_terms',
      name: 'Renewal Terms Negotiation',
      description: 'Negotiate renewal terms and conditions',
      type: 'form_submission',
      required: true,
      order: 2,
      form_schema: {
        sections: [
          {
            title: 'Proposed Terms',
            fields: [
              { name: 'renewal_value', type: 'number', required: true, label: 'Proposed Renewal Value' },
              { name: 'payment_terms', type: 'select', required: true, label: 'Payment Terms', options: ['Net 30', 'Net 45', 'Net 60', 'Advance Payment'] },
              { name: 'service_level', type: 'textarea', required: true, label: 'Service Level Agreement' },
              { name: 'performance_metrics', type: 'textarea', required: true, label: 'Performance Metrics & KPIs' }
            ]
          },
          {
            title: 'Risk Assessment',
            fields: [
              { name: 'risk_level', type: 'select', required: true, label: 'Risk Level', options: ['Low', 'Medium', 'High'] },
              { name: 'risk_factors', type: 'textarea', required: false, label: 'Risk Factors' },
              { name: 'mitigation_measures', type: 'textarea', required: false, label: 'Risk Mitigation Measures' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '3-5 business days'
    },
    {
      id: 'legal_review',
      name: 'Legal Review',
      description: 'Legal team reviews renewal terms',
      type: 'approval',
      required: true,
      order: 3,
      form_schema: {
        sections: [
          {
            title: 'Legal Review',
            fields: [
              { name: 'terms_compliant', type: 'checkbox', required: true, label: 'Terms are legally compliant' },
              { name: 'liability_acceptable', type: 'checkbox', required: true, label: 'Liability terms are acceptable' },
              { name: 'termination_clause', type: 'checkbox', required: true, label: 'Termination clause is appropriate' },
              { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Request Changes'] },
              { name: 'legal_comments', type: 'textarea', required: false, label: 'Legal Comments' }
            ]
          }
        ]
      },
      approval_required: true,
      approver_roles: ['legal_counsel', 'legal_manager'],
      estimated_duration: '3-5 business days'
    },
    {
      id: 'finance_approval',
      name: 'Finance Approval',
      description: 'Finance team approves financial terms',
      type: 'approval',
      required: true,
      order: 4,
      form_schema: {
        sections: [
          {
            title: 'Financial Review',
            fields: [
              { name: 'budget_available', type: 'checkbox', required: true, label: 'Budget is available' },
              { name: 'roi_acceptable', type: 'checkbox', required: true, label: 'ROI is acceptable' },
              { name: 'payment_terms_appropriate', type: 'checkbox', required: true, label: 'Payment terms are appropriate' },
              { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Request Changes'] },
              { name: 'finance_comments', type: 'textarea', required: false, label: 'Finance Comments' }
            ]
          }
        ]
      },
      approval_required: true,
      approver_roles: ['finance_manager', 'cfo'],
      estimated_duration: '2-3 business days'
    },
    {
      id: 'executive_approval',
      name: 'Executive Approval',
      description: 'Final executive approval for contract renewal',
      type: 'approval',
      required: true,
      order: 5,
      form_schema: {
        sections: [
          {
            title: 'Executive Review',
            fields: [
              { name: 'strategic_alignment', type: 'checkbox', required: true, label: 'Renewal aligns with strategic goals' },
              { name: 'risk_acceptable', type: 'checkbox', required: true, label: 'Risk level is acceptable' },
              { name: 'value_proposition', type: 'checkbox', required: true, label: 'Value proposition is clear' },
              { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Request Changes'] },
              { name: 'executive_comments', type: 'textarea', required: false, label: 'Executive Comments' }
            ]
          }
        ]
      },
      approval_required: true,
      approver_roles: ['ceo', 'executive_team'],
      estimated_duration: '2-3 business days'
    },
    {
      id: 'contract_execution',
      name: 'Contract Execution',
      description: 'Execute and finalize renewed contract',
      type: 'automation',
      required: true,
      order: 6,
      automated_actions: [
        { action: 'generate_renewal_contract', trigger: 'step_start' },
        { action: 'send_for_signature', trigger: 'step_start' },
        { action: 'update_contract_system', trigger: 'step_complete' },
        { action: 'notify_stakeholders', trigger: 'step_complete' }
      ],
      estimated_duration: '1-2 business days'
    }
  ];
  
  export default steps;