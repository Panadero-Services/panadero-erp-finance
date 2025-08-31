export const steps = [
    {
      id: 'budget_preparation',
      name: 'Budget Preparation',
      description: 'Department managers prepare annual budget',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Budget Overview',
            fields: [
              { name: 'department', type: 'select', required: true, label: 'Department' },
              { name: 'budget_year', type: 'number', required: true, label: 'Budget Year' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '1-2 weeks'
    }
  ];
  
  export default steps;