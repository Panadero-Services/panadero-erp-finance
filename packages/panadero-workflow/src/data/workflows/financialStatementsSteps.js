export const steps = [
  {
    id: 'statement_preparation',
    name: 'Statement Preparation',
    description: 'Prepare monthly financial statements',
    type: 'form_submission',
    required: true,
    order: 1,
    form_schema: {
      sections: [
        {
          title: 'Reporting Period',
          fields: [
            { name: 'reporting_month', type: 'month', required: true, label: 'Reporting Month' },
            { name: 'reporting_year', type: 'number', required: true, label: 'Reporting Year' },
            { name: 'prepared_by', type: 'text', required: true, label: 'Prepared By' },
            { name: 'preparation_date', type: 'date', required: true, label: 'Preparation Date' }
          ]
        },
        {
          title: 'Statement Components',
          fields: [
            { name: 'income_statement', type: 'checkbox', required: true, label: 'Income Statement Complete' },
            { name: 'balance_sheet', type: 'checkbox', required: true, label: 'Balance Sheet Complete' },
            { name: 'cash_flow_statement', type: 'checkbox', required: true, label: 'Cash Flow Statement Complete' },
            { name: 'notes_prepared', type: 'checkbox', required: true, label: 'Notes to Financial Statements Prepared' },
            { name: 'variance_analysis', type: 'checkbox', required: true, label: 'Variance Analysis Complete' }
          ]
        }
      ]
    },
    validation_rules: {
      required_fields: ['reporting_month', 'reporting_year', 'prepared_by'],
      custom_validations: [
        { rule: 'all_statements_complete', message: 'All financial statements must be complete' },
        { rule: 'balances_reconciled', message: 'All balances must be reconciled' }
      ]
    },
    approval_required: false,
    estimated_duration: '3-5 business days'
  },
  {
    id: 'accounting_review',
    name: 'Accounting Review',
    description: 'Accounting manager reviews financial statements',
    type: 'approval',
    required: true,
    order: 2,
    form_schema: {
      sections: [
        {
          title: 'Accounting Review',
          fields: [
            { name: 'calculations_correct', type: 'checkbox', required: true, label: 'All calculations are correct' },
            { name: 'gaap_compliance', type: 'checkbox', required: true, label: 'GAAP compliance verified' },
            { name: 'variances_explained', type: 'checkbox', required: true, label: 'Material variances explained' },
            { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Request Corrections'] },
            { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
          ]
        }
      ]
    },
    approval_required: true,
    approver_roles: ['accounting_manager'],
    estimated_duration: '2-3 business days'
  },
  {
    id: 'executive_review',
    name: 'Executive Review',
    description: 'Executive team reviews and approves statements',
    type: 'approval',
    required: true,
    order: 3,
    form_schema: {
      sections: [
        {
          title: 'Executive Review',
          fields: [
            { name: 'performance_review', type: 'checkbox', required: true, label: 'Financial performance reviewed' },
            { name: 'strategic_alignment', type: 'checkbox', required: true, label: 'Results align with strategic goals' },
            { name: 'approved_for_distribution', type: 'radio', required: true, label: 'Decision', options: ['Approve for Distribution', 'Request Additional Analysis'] },
            { name: 'distribution_list', type: 'multi-select', required: true, label: 'Distribution List', options: ['Board of Directors', 'Investors', 'Lenders', 'Management Team', 'Department Heads'] },
            { name: 'comments', type: 'textarea', required: false, label: 'Executive Comments' }
          ]
        }
      ]
    },
    approval_required: true,
    approver_roles: ['cfo', 'ceo'],
    automated_actions: [
      { action: 'distribute_statements', trigger: 'approval_complete' },
      { action: 'archive_statements', trigger: 'distribution_complete' }
    ],
    estimated_duration: '2-3 business days'
  }
];

export default steps;