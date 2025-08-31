export const steps = [
    {
      id: 'invoice_creation',
      name: 'Invoice Creation',
      description: 'Create customer invoice with line items',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Customer Information',
            fields: [
              { name: 'customer_id', type: 'select', required: true, label: 'Customer' },
              { name: 'invoice_date', type: 'date', required: true, label: 'Invoice Date' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '30 minutes'
    }
  ];
  
  export default steps;