export const steps = [
    {
      id: 'invoice_review',
      name: 'Invoice Review & Entry',
      description: 'Review vendor invoice and enter into system',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Invoice Details',
            fields: [
              { name: 'vendor_id', type: 'select', required: true, label: 'Vendor' },
              { name: 'invoice_number', type: 'text', required: true, label: 'Invoice Number' },
              { name: 'total_amount', type: 'number', required: true, label: 'Total Amount' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '20 minutes'
    }
  ];
  
  export default steps;