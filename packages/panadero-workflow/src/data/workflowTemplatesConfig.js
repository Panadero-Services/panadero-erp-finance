/**
 * Maintainable Workflow Templates Configuration
 * Modular structure with separate files for complex definitions
 */

// ===== BASIC TEMPLATE DEFINITIONS =====
const BASIC_TEMPLATES = [
  // Core Business Workflows
  { name: 'Vendor Onboarding', id: 'vendor-onboarding', steps: 5, avgTime: '3-5 days', category: 'procurement', module: 'demo', entity: 'finance_vendors', complexity: 'high', description: 'Complete vendor registration and validation process' },
  { name: 'Employee Vacancy', id: 'employee-vacancy', steps: 5, avgTime: '2-3 weeks', category: 'hr', module: 'demo', entity: 'employee_vacancies', complexity: 'medium', description: 'Complete employee vacancy posting and hiring process' },
  { name: 'Employee Onboarding', id: 'employee-onboarding', steps: 7, avgTime: '1-2 weeks', category: 'hr', module: 'demo', entity: 'employees', complexity: 'high', description: 'Complete employee onboarding process' },
  { name: 'Purchase Request', id: 'purchase-request', steps: 4, avgTime: '2-3 days', category: 'procurement', module: 'demo', entity: 'purchase_requests', complexity: 'medium', description: 'Request approval for purchasing goods or services' },
  { name: 'Contract Renewal', id: 'contract-renewal', steps: 6, avgTime: '2-3 weeks', category: 'legal', module: 'demo', entity: 'contracts', complexity: 'medium', description: 'Review and renew existing contracts' },
  
  // Financial ERP Workflows
  { name: 'Simple Journal Entry', id: 'gl-journal-entry-simple', steps: 2, avgTime: '30 minutes', category: 'general_ledger', module: 'demo', entity: 'journal_entries', complexity: 'low', description: 'Basic journal entry with standard debit/credit validation' },
  { name: 'Vendor Invoice Approval', id: 'ap-vendor-invoice-approval', steps: 3, avgTime: '3-5 days', category: 'accounts_payable', module: 'demo', entity: 'vendor_invoices', complexity: 'medium', description: 'Multi-level approval process for vendor invoices' },
  { name: 'Customer Invoice Creation', id: 'ar-customer-invoice-creation', steps: 2, avgTime: '1-2 days', category: 'accounts_receivable', module: 'demo', entity: 'customer_invoices', complexity: 'medium', description: 'Create and process customer invoices' },
  { name: 'Cash Flow Forecast', id: 'cf-cash-forecast-approval', steps: 2, avgTime: '2-3 days', category: 'cash_flow', module: 'demo', entity: 'cash_flow_forecasts', complexity: 'medium', description: 'Create and approve monthly cash flow forecasts' },
  { name: 'Department Budget Approval', id: 'budget-department-budget-approval', steps: 3, avgTime: '3-4 weeks', category: 'budgeting', module: 'demo', entity: 'department_budgets', complexity: 'high', description: 'Annual department budget planning and approval' },
  { name: 'Asset Acquisition', id: 'fa-asset-acquisition-approval', steps: 3, avgTime: '1-2 weeks', category: 'fixed_assets', module: 'demo', entity: 'fixed_assets', complexity: 'medium', description: 'Approval process for acquiring fixed assets' },
  { name: 'Quarterly Tax Filing', id: 'tax-quarterly-filing-preparation', steps: 3, avgTime: '1 week', category: 'tax_management', module: 'demo', entity: 'tax_filings', complexity: 'high', description: 'Prepare and file quarterly tax returns' },
  { name: 'Monthly Reconciliation', id: 'compliance-monthly-reconciliation', steps: 2, avgTime: '3-5 days', category: 'compliance', module: 'demo', entity: 'reconciliations', complexity: 'medium', description: 'Monthly account reconciliation and compliance review' },
  { name: 'Financial Statements', id: 'report-financial-statements', steps: 3, avgTime: '1-2 weeks', category: 'reporting', module: 'demo', entity: 'financial_statements', complexity: 'high', description: 'Prepare and review monthly financial statements' }
];

// ===== WORKFLOW DEFINITIONS (Lazy loaded when needed) =====
const WORKFLOW_DEFINITIONS = {
  'vendor-onboarding': () => import('./workflows/VendorOnboardingWorkflow.js'),
  'employee-vacancy': () => import('./workflows/EmployeeVacancyWorkflow.js'),
  'employee-onboarding': () => import('./workflows/employeeOnboardingSteps.js'),
  'purchase-request': () => import('./workflows/purchaseRequestSteps.js'),
  'contract-renewal': () => import('./workflows/contractRenewalSteps.js'),
  'gl-journal-entry-simple': () => import('./workflows/journalEntrySteps.js'),
  'ap-vendor-invoice-approval': () => import('./workflows/invoiceApprovalSteps.js'),
  'ar-customer-invoice-creation': () => import('./workflows/customerInvoiceSteps.js'),
  'cf-cash-forecast-approval': () => import('./workflows/cashFlowSteps.js'),
  'budget-department-budget-approval': () => import('./workflows/budgetApprovalSteps.js'),
  'fa-asset-acquisition-approval': () => import('./workflows/assetAcquisitionSteps.js'),
  'tax-quarterly-filing-preparation': () => import('./workflows/taxFilingSteps.js'),
  'compliance-monthly-reconciliation': () => import('./workflows/reconciliationSteps.js'),
  'report-financial-statements': () => import('./workflows/financialStatementsSteps.js')
};

// ===== ENHANCED TEMPLATES WITH WORKFLOW LOADING =====
function enhanceTemplateWithWorkflows(template) {
  return {
    ...template,
    estimated_duration: template.avgTime,
    approval_levels: template.complexity === 'high' ? 3 : template.complexity === 'medium' ? 2 : 0,
    module: template.category.split('_')[0],
    version: '1.0.8',
    
    // Lazy load detailed workflows when needed
    async getSteps() {
      if (WORKFLOW_DEFINITIONS[template.id]) {
        try {
          const workflowModule = await WORKFLOW_DEFINITIONS[template.id]();
          return workflowModule.default || workflowModule.steps;
        } catch (error) {
          console.warn(`Failed to load workflow for ${template.id}:`, error);
          return [];
        }
      }
      return [];
    }
  };
}

// ===== CATEGORIES =====
export const WORKFLOW_CATEGORIES = {
  procurement: 'Procurement',
  hr: 'Human Resources', 
  legal: 'Legal',
  general_ledger: 'General Ledger',
  accounts_payable: 'Accounts Payable',
  accounts_receivable: 'Accounts Receivable',
  cash_flow: 'Cash Flow',
  budgeting: 'Budgeting',
  fixed_assets: 'Fixed Assets',
  tax_management: 'Tax Management',
  compliance: 'Compliance',
  reporting: 'Reporting'
};

// ===== WORKFLOW STEP TYPES =====
export const WORKFLOW_STEPTYPES = [
  'shared_entity_selection', 
  'form_submission', 
  'approval', 
  'checklist', 
  'submit_database' 
];

// ===== UTILITY FUNCTIONS =====
export function getAllWorkflowTemplates() {
  return BASIC_TEMPLATES.map(enhanceTemplateWithWorkflows);
}

export function getWorkflowTemplateById(id) {
  const template = BASIC_TEMPLATES.find(t => t.id === id);
  return template ? enhanceTemplateWithWorkflows(template) : null;
}

export function getWorkflowsByCategory(category) {
  return BASIC_TEMPLATES
    .filter(t => t.category === category)
    .map(enhanceTemplateWithWorkflows);
}

export default getAllWorkflowTemplates();