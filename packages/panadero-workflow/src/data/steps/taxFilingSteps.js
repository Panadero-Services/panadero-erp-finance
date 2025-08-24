export const steps = [
    {
      id: 'data_preparation',
      name: 'Tax Data Preparation',
      description: 'Gather and prepare tax data for quarterly filing',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Filing Information',
            fields: [
              { name: 'tax_quarter', type: 'select', required: true, label: 'Tax Quarter', options: ['Q1', 'Q2', 'Q3', 'Q4'] },
              { name: 'tax_year', type: 'number', required: true, label: 'Tax Year' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '2-3 business days'
    }
  ];
  
  export default steps;