export const steps = [
    {
      id: 'request_creation',
      name: 'Create Purchase Request',
      description: 'Submit request for purchasing approval',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Request Details',
            fields: [
              { name: 'request_type', type: 'select', required: true, label: 'Request Type', options: ['Goods', 'Services', 'Equipment'] },
              { name: 'total_amount', type: 'number', required: true, label: 'Total Amount' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '30 minutes'
    }
  ];
  
  export default steps;