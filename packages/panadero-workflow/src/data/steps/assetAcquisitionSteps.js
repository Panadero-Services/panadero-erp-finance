export const steps = [
    {
      id: 'asset_request',
      name: 'Asset Request',
      description: 'Submit request for fixed asset acquisition',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Asset Information',
            fields: [
              { name: 'asset_type', type: 'select', required: true, label: 'Asset Type', options: ['Equipment', 'Vehicles', 'Furniture'] },
              { name: 'estimated_cost', type: 'number', required: true, label: 'Estimated Cost' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '1-2 hours'
    }
  ];
  
  export default steps;