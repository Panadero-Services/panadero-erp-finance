export const steps = [
  {
    id: 'reconciliation_prep',
    name: 'Reconciliation Preparation',
    description: 'Prepare monthly account reconciliations',
    type: 'form_submission',
    required: true,
    order: 1,
    form_schema: {
      sections: [
        {
          title: 'Reconciliation Details',
          fields: [
            { name: 'reconciliation_month', type: 'month', required: true, label: 'Reconciliation Month' },
            { name: 'account_type', type: 'select', required: true, label: 'Account Type', options: ['Bank Accounts', 'Credit Cards', 'Loans', 'Investments', 'All Accounts'] },
            { name: 'prepared_by', type: 'text', required: true, label: 'Prepared By' },
            { name: 'preparation_date', type: 'date', required: true, label: 'Preparation Date' }
          ]
        },
        {
          title: 'Reconciliation Results',
          fields: [
            { name: 'accounts_reconciled', type: 'number', required: true, label: 'Number of Accounts Reconciled' },
            { name: 'total_adjustments', type: 'number', required: true, label: 'Total Adjustments Made' },
            { name: 'unreconciled_items', type: 'number', required: true, label: 'Unreconciled Items' },
            { name: 'variance_amount', type: 'number', required: true, label: 'Total Variance Amount' },
            { name: 'variance_explanation', type: 'textarea', required: false, label: 'Variance Explanation' }
          ]
        }
      ]
    },
    validation_rules: {
      required_fields: ['reconciliation_month', 'account_type', 'accounts_reconciled'],
      custom_validations: [
        { rule: 'variance_within_tolerance', message: 'Variance must be within acceptable tolerance' },
        { rule: 'all_items_addressed', message: 'All unreconciled items must be addressed' }
      ]
    },
    approval_required: false,
    estimated_duration: '2-3 business days'
  },
  {
    id: 'reconciliation_review',
    name: 'Reconciliation Review',
    description: 'Manager reviews and approves reconciliation',
    type: 'approval',
    required: true,
    order: 2,
    form_schema: {
      sections: [
        {
          title: 'Review & Approval',
          fields: [
            { name: 'reconciliations_complete', type: 'checkbox', required: true, label: 'All reconciliations are complete' },
            { name: 'variances_explained', type: 'checkbox', required: true, label: 'All variances are explained' },
            { name: 'supporting_docs', type: 'checkbox', required: true, label: 'Supporting documentation is adequate' },
            { name: 'approved', type: 'radio', required: true, label: 'Decision', options: ['Approve', 'Request Additional Work'] },
            { name: 'comments', type: 'textarea', required: false, label: 'Comments' }
          ]
        }
      ]
    },
    approval_required: true,
    approver_roles: ['accounting_manager', 'finance_manager'],
    estimated_duration: '1-2 business days'
  }
];

export default steps;