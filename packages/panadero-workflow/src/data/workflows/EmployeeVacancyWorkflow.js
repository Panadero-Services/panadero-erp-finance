/**
 * Employee Vacancy Workflow
 * @version 1.0.0
 * Complete employee vacancy posting and hiring process
 * @description 5-step process: Job Requisition → Management Approval → Job Posting → Pre-screening → Final Approval
 */

export const steps = [
  {
    id: 'job_requisition',
    name: 'Job Requisition Creation',
    description: 'Create job requisition with requirements and salary range',
    type: 'form_submission',
    entity_type: 'employee_vacancy',
    required: true,
    order: 1,
    form_schema: {
      sections: [
        {
          title: 'Job Details',
          fields: [
            {
              name: 'job_title',
              label: 'Job Title',
              type: 'text',
              required: true,
              validation: 'required|string|max:100',
              placeholder: 'e.g., Senior Software Engineer'
            },
            {
              name: 'department',
              label: 'Department',
              type: 'select',
              required: true,
              options: ['IT', 'HR', 'Finance', 'Marketing', 'Sales', 'Operations', 'Customer Service'],
              validation: 'required|string'
            },
            {
              name: 'job_level',
              label: 'Job Level',
              type: 'select',
              required: true,
              options: ['Entry', 'Mid', 'Senior', 'Lead', 'Manager', 'Director'],
              validation: 'required|string'
            },
            {
              name: 'employment_type',
              label: 'Employment Type',
              type: 'select',
              required: true,
              options: ['Full-time', 'Part-time', 'Contract', 'Internship'],
              validation: 'required|string'
            }
          ]
        },
        {
          title: 'Compensation & Benefits',
          fields: [
            {
              name: 'salary_range_min',
              label: 'Minimum Salary',
              type: 'number',
              required: true,
              validation: 'required|numeric|min:0',
              placeholder: '50000'
            },
            {
              name: 'salary_range_max',
              label: 'Maximum Salary',
              type: 'number',
              required: true,
              validation: 'required|numeric|min:0',
              placeholder: '80000'
            },
            {
              name: 'benefits_package',
              label: 'Benefits Package',
              type: 'textarea',
              required: false,
              placeholder: 'Health insurance, 401k, PTO, etc.'
            }
          ]
        },
        {
          title: 'Requirements',
          fields: [
            {
              name: 'education_required',
              label: 'Education Required',
              type: 'select',
              required: true,
              options: ['High School', 'Associate', 'Bachelor', 'Master', 'PhD', 'None'],
              validation: 'required|string'
            },
            {
              name: 'experience_years',
              label: 'Years of Experience Required',
              type: 'number',
              required: true,
              validation: 'required|numeric|min:0|max:50',
              placeholder: '3'
            },
            {
              name: 'skills_required',
              label: 'Required Skills',
              type: 'textarea',
              required: true,
              validation: 'required|string|min:10',
              placeholder: 'List key skills and technologies required'
            }
          ]
        }
      ]
    },
    approval_required: false,
    estimated_duration: '30 minutes'
  },
  {
    id: 'management_approval',
    name: 'Management Approval',
    description: 'Get approval from department head and HR manager',
    type: 'approval',
    entity_type: 'employee_vacancy',
    required: true,
    order: 2,
    approvers: [
      {
        id: 'dept_head',
        name: 'Department Head',
        required: true,
        role: 'department_manager'
      },
      {
        id: 'hr_manager',
        name: 'HR Manager',
        required: true,
        role: 'hr_manager'
      }
    ],
    approval_required: true,
    estimated_duration: '2-3 days'
  },
  {
    id: 'job_posting',
    name: 'Job Posting Creation',
    description: 'Create job posting for external platforms and company website',
    type: 'form_submission',
    entity_type: 'employee_vacancy',
    required: true,
    order: 3,
    form_schema: {
      sections: [
        {
          title: 'Job Description',
          fields: [
            {
              name: 'job_description',
              label: 'Detailed Job Description',
              type: 'textarea',
              required: true,
              validation: 'required|string|min:100',
              placeholder: 'Provide a comprehensive job description...'
            },
            {
              name: 'key_responsibilities',
              label: 'Key Responsibilities',
              type: 'textarea',
              required: true,
              validation: 'required|string|min:50',
              placeholder: 'List main job responsibilities...'
            },
            {
              name: 'qualifications',
              label: 'Required Qualifications',
              type: 'textarea',
              required: true,
              validation: 'required|string|min:50',
              placeholder: 'List required qualifications...'
            }
          ]
        },
        {
          title: 'Posting Configuration',
          fields: [
            {
              name: 'posting_platforms',
              label: 'Posting Platforms',
              type: 'checkbox',
              options: ['LinkedIn', 'Indeed', 'Company Website', 'Glassdoor', 'AngelList', 'Other Job Boards'],
              required: true
            },
            {
              name: 'application_deadline',
              label: 'Application Deadline',
              type: 'date',
              required: true,
              validation: 'required|date|after:today'
            },
            {
              name: 'start_date',
              label: 'Expected Start Date',
              type: 'date',
              required: true,
              validation: 'required|date|after:today'
            }
          ]
        }
      ]
    },
    approval_required: false,
    estimated_duration: '45 minutes'
  },
  {
    id: 'pre_screening',
    name: 'Pre-screening Checklist',
    description: 'Complete pre-screening requirements and interview preparation',
    type: 'checklist',
    entity_type: 'employee_vacancy',
    required: true,
    order: 4,
    checklist_items: [
      'Background check policy reviewed and approved',
      'Interview questions prepared for all rounds',
      'Assessment tests ready (if applicable)',
      'Reference check process defined',
      'Interview panel members confirmed',
      'Interview schedule template created',
      'Evaluation criteria established',
      'Onboarding process reviewed for new role'
    ],
    approval_required: false,
    estimated_duration: '1-2 hours'
  },
  {
    id: 'final_approval',
    name: 'Final HR Approval',
    description: 'Final approval from HR Director before posting goes live',
    type: 'approval',
    entity_type: 'employee_vacancy',
    required: true,
    order: 5,
    approvers: [
      {
        id: 'hr_director',
        name: 'HR Director',
        required: true,
        role: 'hr_director'
      }
    ],
    approval_required: true,
    estimated_duration: '1 day'
  }
]
