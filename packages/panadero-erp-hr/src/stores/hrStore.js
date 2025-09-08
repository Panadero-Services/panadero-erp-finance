import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import Decimal from 'decimal.js';
import axios from 'axios';

export const useHrStore = defineStore('hr', () => {
  // Settings - centralized configuration
  const settings = ref({
    // Font scaling
    fontSize: 14,
    
    // Display
    darkMode: true,
    compactLayout: false,
    showDebug: false,
    
    // Tables
    rowsPerPage: 25,
    showRowNumbers: true,
    stickyHeaders: true,
    
    // Export
    defaultExportFormat: 'csv',
    includeHeaders: true,
    autoExport: false,
    
    // Data
    autoRefreshInterval: 30,
    lazyLoading: true,
    cacheData: true
  });

  // Settings actions
  const updateSetting = (key, value) => {
    settings.value[key] = value;
    localStorage.setItem('hrSettings', JSON.stringify(settings.value));
  };

  const updateSettings = (newSettings) => {
    Object.assign(settings.value, newSettings);
    localStorage.setItem('hrSettings', JSON.stringify(newSettings));
  };

  const resetSettings = () => {
    settings.value = {
      fontSize: 14,
      darkMode: true,
      compactLayout: false,
      showDebug: false,
      rowsPerPage: 25,
      showRowNumbers: true,
      stickyHeaders: true,
      defaultExportFormat: 'csv',
      includeHeaders: true,
      autoExport: false,
      autoRefreshInterval: 30,
      lazyLoading: true,
      cacheData: true
    };
    localStorage.setItem('hrSettings', JSON.stringify(settings.value));
  };

  const loadSettings = () => {
    const saved = localStorage.getItem('hrSettings');
    if (saved) {
      try {
        const parsed = JSON.parse(saved);
        Object.assign(settings.value, parsed);
      } catch (err) {
        console.error('Failed to load saved settings:', err);
      }
    }
  };

  // Font size specific actions
  const setFontSize = (size) => {
    settings.value.fontSize = size;
    localStorage.setItem('hrSettings', JSON.stringify(settings.value));
  };

  // Core HR state (demo data)
  const employees = ref([
    {
      id: 1,
      employeeId: 'EMP001',
      firstName: 'John',
      lastName: 'Smith',
      email: 'john.smith@company.com',
      phone: '+1-555-0101',
      department: 'Human Resources',
      position: 'HR Manager',
      employmentType: 'full_time',
      hireDate: '2020-01-15',
      status: 'active',
      salary: 75000.00,
      skills: ['Recruitment', 'Employee Relations', 'Performance Management', 'HRIS']
    },
    {
      id: 2,
      employeeId: 'EMP002',
      firstName: 'Sarah',
      lastName: 'Johnson',
      email: 'sarah.johnson@company.com',
      phone: '+1-555-0201',
      department: 'Engineering',
      position: 'Senior Developer',
      employmentType: 'full_time',
      hireDate: '2019-06-01',
      status: 'active',
      salary: 95000.00,
      skills: ['JavaScript', 'Vue.js', 'Node.js', 'Python', 'AWS']
    },
    {
      id: 3,
      employeeId: 'EMP003',
      firstName: 'Michael',
      lastName: 'Brown',
      email: 'michael.brown@company.com',
      phone: '+1-555-0301',
      department: 'Marketing',
      position: 'Marketing Director',
      employmentType: 'full_time',
      hireDate: '2018-03-10',
      status: 'active',
      salary: 85000.00,
      skills: ['Digital Marketing', 'SEO', 'Content Strategy', 'Analytics', 'Brand Management']
    }
  ]);

  const departments = ref([
    {
      id: 1,
      name: 'Human Resources',
      description: 'Manages employee relations, recruitment, and organizational development',
      code: 'HR',
      budget: 500000.00,
      location: 'Headquarters - Floor 3',
      status: 'active',
      employeeCount: 5
    },
    {
      id: 2,
      name: 'Engineering',
      description: 'Software development and technical innovation',
      code: 'ENG',
      budget: 2000000.00,
      location: 'Headquarters - Floor 2',
      status: 'active',
      employeeCount: 12
    },
    {
      id: 3,
      name: 'Marketing',
      description: 'Brand management, digital marketing, and customer acquisition',
      code: 'MKT',
      budget: 800000.00,
      location: 'Headquarters - Floor 1',
      status: 'active',
      employeeCount: 8
    }
  ]);

  const vacancies = ref([
    {
      id: 1,
      title: 'Senior Frontend Developer',
      description: 'We are looking for an experienced Frontend Developer to join our engineering team.',
      department: 'Engineering',
      positionType: 'Senior Software Engineer',
      employmentType: 'full_time',
      experienceLevel: 'senior',
      location: 'San Francisco, CA',
      remoteAllowed: true,
      salaryMin: 90000.00,
      salaryMax: 130000.00,
      status: 'published',
      applicationCount: 15,
      createdDate: '2025-08-15'
    },
    {
      id: 2,
      title: 'Marketing Specialist',
      description: 'Join our marketing team as a Marketing Specialist to help drive brand awareness.',
      department: 'Marketing',
      positionType: 'Marketing Specialist',
      employmentType: 'full_time',
      experienceLevel: 'mid',
      location: 'New York, NY',
      remoteAllowed: true,
      salaryMin: 55000.00,
      salaryMax: 75000.00,
      status: 'published',
      applicationCount: 8,
      createdDate: '2025-08-20'
    }
  ]);

  const applications = ref([
    {
      id: 1,
      vacancyId: 1,
      firstName: 'Alex',
      lastName: 'Thompson',
      email: 'alex.thompson@email.com',
      phone: '+1-555-1001',
      status: 'screening',
      aiScore: 85,
      appliedDate: '2025-08-25',
      experience: '6 years',
      skills: ['Vue.js', 'React', 'JavaScript', 'TypeScript']
    },
    {
      id: 2,
      vacancyId: 1,
      firstName: 'Maria',
      lastName: 'Garcia',
      email: 'maria.garcia@email.com',
      phone: '+1-555-1002',
      status: 'interview',
      aiScore: 92,
      appliedDate: '2025-08-26',
      experience: '7 years',
      skills: ['Vue.js', 'React', 'Angular', 'JavaScript', 'TypeScript']
    }
  ]);

  const performanceReviews = ref([
    {
      id: 1,
      employeeId: 1,
      employeeName: 'John Smith',
      reviewPeriod: 'Q3 2025',
      reviewDate: '2025-09-30',
      overallRating: 5,
      status: 'completed',
      goals: ['Complete recruitment for 5 open positions', 'Implement new HRIS system'],
      achievements: ['Successfully recruited 6 candidates', 'HRIS implementation completed on time']
    },
    {
      id: 2,
      employeeId: 2,
      employeeName: 'Sarah Johnson',
      reviewPeriod: 'Q3 2025',
      reviewDate: '2025-09-30',
      overallRating: 5,
      status: 'completed',
      goals: ['Complete 3 major feature developments', 'Improve code quality metrics'],
      achievements: ['Completed 4 major features', 'Code quality score improved to 95%']
    }
  ]);

  // Agent Portal data
  const agentPrompts = ref([
    {
      id: 1,
      title: 'Resume Analysis',
      prompt: 'Analyze the provided resume and provide insights on candidate qualifications, skills match, and recommendations',
      category: 'recruitment',
      active: true
    },
    {
      id: 2,
      title: 'Job Matching',
      prompt: 'Match candidate profiles with available job positions based on skills, experience, and requirements',
      category: 'recruitment',
      active: true
    },
    {
      id: 3,
      title: 'Performance Review Analysis',
      prompt: 'Analyze performance review data and provide insights on employee development and team performance trends',
      category: 'performance',
      active: true
    },
    {
      id: 4,
      title: 'HR Analytics',
      prompt: 'Provide comprehensive HR analytics including turnover rates, hiring trends, and department performance metrics',
      category: 'analytics',
      active: true
    },
    {
      id: 5,
      title: 'Employee Engagement',
      prompt: 'Analyze employee engagement data and suggest strategies to improve workplace satisfaction and retention',
      category: 'engagement',
      active: true
    }
  ]);

  // Getters
  const getEmployees = computed(() => (filters = {}) => {
    let filtered = employees.value;
    if (filters.department) filtered = filtered.filter(emp => emp.department === filters.department);
    if (filters.status) filtered = filtered.filter(emp => emp.status === filters.status);
    if (filters.employmentType) filtered = filtered.filter(emp => emp.employmentType === filters.employmentType);
    return filtered;
  });

  const getActiveEmployees = computed(() => 
    employees.value.filter(emp => emp.status === 'active')
  );

  const getTotalEmployees = computed(() => employees.value.length);

  const getActiveVacancies = computed(() => 
    vacancies.value.filter(vacancy => vacancy.status === 'published')
  );

  const getTotalApplications = computed(() => applications.value.length);

  const getPendingApplications = computed(() => 
    applications.value.filter(app => ['submitted', 'screening'].includes(app.status))
  );

  // HR Management actions
  async function addEmployee(employee) {
    const newEmployee = { 
      ...employee, 
      id: Date.now(), 
      status: 'active', 
      hireDate: new Date().toISOString().split('T')[0] 
    };
    employees.value.push(newEmployee);
    return newEmployee;
  }

  async function updateEmployee(employeeId, updates) {
    const index = employees.value.findIndex(emp => emp.id === employeeId);
    if (index !== -1) {
      employees.value[index] = { ...employees.value[index], ...updates };
    }
  }

  async function addVacancy(vacancy) {
    const newVacancy = { 
      ...vacancy, 
      id: Date.now(), 
      status: 'draft', 
      applicationCount: 0,
      createdDate: new Date().toISOString().split('T')[0]
    };
    vacancies.value.push(newVacancy);
    return newVacancy;
  }

  async function updateVacancy(vacancyId, updates) {
    const index = vacancies.value.findIndex(vac => vac.id === vacancyId);
    if (index !== -1) {
      vacancies.value[index] = { ...vacancies.value[index], ...updates };
    }
  }

  async function addApplication(application) {
    const newApplication = { 
      ...application, 
      id: Date.now(), 
      status: 'submitted',
      appliedDate: new Date().toISOString().split('T')[0]
    };
    applications.value.push(newApplication);
    
    // Update vacancy application count
    const vacancy = vacancies.value.find(vac => vac.id === application.vacancyId);
    if (vacancy) {
      vacancy.applicationCount += 1;
    }
    
    return newApplication;
  }

  async function updateApplicationStatus(applicationId, status) {
    const index = applications.value.findIndex(app => app.id === applicationId);
    if (index !== -1) {
      applications.value[index].status = status;
    }
  }

  async function addDepartment(department) {
    const newDepartment = { 
      ...department, 
      id: Date.now(), 
      status: 'active',
      employeeCount: 0
    };
    departments.value.push(newDepartment);
    return newDepartment;
  }

  async function generateHrReport(reportType, filters = {}) {
    const report = {
      id: Date.now(),
      reportType: reportType,
      title: `Generated ${reportType} Report`,
      generatedAt: new Date().toISOString(),
      data: { /* Report data based on type */ }
    };
    return report;
  }

  // Agent Portal actions
  async function addAgentPrompt(prompt) {
    const newPrompt = { ...prompt, id: Date.now(), active: true };
    agentPrompts.value.push(newPrompt);
    return newPrompt;
  }

  async function executeAgentPrompt(promptId, context = {}) {
    const prompt = agentPrompts.value.find(p => p.id === promptId);
    if (prompt) {
      // Simulate AI processing
      console.log('Executing agent prompt:', prompt.title);
      console.log('Context:', context);
      return { success: true, result: 'AI processing completed for HR data' };
    }
    return { success: false, error: 'Prompt not found' };
  }

  return {
    // Settings
    settings,
    updateSetting,
    updateSettings,
    resetSettings,
    loadSettings,
    setFontSize,
    
    // Core data
    employees,
    departments,
    vacancies,
    applications,
    performanceReviews,
    agentPrompts,
    
    // Getters
    getEmployees,
    getActiveEmployees,
    getTotalEmployees,
    getActiveVacancies,
    getTotalApplications,
    getPendingApplications,
    
    // Actions
    addEmployee,
    updateEmployee,
    addVacancy,
    updateVacancy,
    addApplication,
    updateApplicationStatus,
    addDepartment,
    generateHrReport,
    addAgentPrompt,
    executeAgentPrompt
  };
});
