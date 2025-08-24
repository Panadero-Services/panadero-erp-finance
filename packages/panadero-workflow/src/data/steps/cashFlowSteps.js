export const steps = [
    {
      id: 'forecast_preparation',
      name: 'Forecast Preparation',
      description: 'Prepare monthly cash flow forecast',
      type: 'form_submission',
      required: true,
      order: 1,
      form_schema: {
        sections: [
          {
            title: 'Forecast Period',
            fields: [
              { name: 'forecast_month', type: 'month', required: true, label: 'Forecast Month' },
              { name: 'forecast_year', type: 'number', required: true, label: 'Forecast Year' }
            ]
          }
        ]
      },
      approval_required: false,
      estimated_duration: '4-6 hours'
    }
  ];
  
  export default steps;