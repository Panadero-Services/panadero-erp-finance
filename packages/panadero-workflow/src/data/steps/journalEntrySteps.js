export const steps = [
    {
      id: 'entry_creation',
      name: 'Journal Entry Creation',
      description: 'Create basic journal entry with account lines',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Entry Header',
            fields: [
              { name: 'entry_date', type: 'date', required: true, label: 'Entry Date' },
              { name: 'reference', type: 'text', required: true, label: 'Reference Number' },
              { name: 'description', type: 'textarea', required: true, label: 'Description' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '15 minutes'
    },
    {
      id: 'validation',
      name: 'Entry Validation',
      description: 'Validate journal entry for accuracy',
      type: 'financial_validation',
      required: true,
      order: 2,
      form_schema: {
        sections: [
          {
            title: 'Validation Checklist',
            fields: [
              { name: 'accounts_exist', type: 'checkbox', required: true, label: 'All accounts exist and are active' },
              { name: 'period_open', type: 'checkbox', required: true, label: 'Accounting period is open' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '5 minutes'
    }
  ];
  
  export default steps;