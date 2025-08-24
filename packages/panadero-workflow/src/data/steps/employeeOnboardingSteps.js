export const steps = [
    {
      id: 'pre_boarding',
      name: 'Pre-boarding Setup',
      description: 'Prepare for new employee arrival',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Employee Information',
            fields: [
              { name: 'employee_name', type: 'text', required: true, label: 'Employee Name' },
              { name: 'position', type: 'text', required: true, label: 'Position' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '2 hours'
    }
  ];
  
  export default steps;