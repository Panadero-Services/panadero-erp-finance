/**
 * Maintainable Workflow Templates Configuration
 * Modular structure with separate files for complex definitions
 */

// ===== BASIC TEMPLATE DEFINITIONS =====
const BASIC_TEMPLATES = [
  // Core Business Workflows
  { name: 'Vendor Onboarding', id: 'vendor-onboarding', steps: 5, avgTime: '3-5 days', category: 'procurement', complexity: 'high', description: 'Complete vendor registration and validation process' },
  { name: 'Employee Onboarding', id: 'employee-onboarding', steps: 7, avgTime: '1-2 weeks', category: 'hr', complexity: 'high', description: 'Complete employee onboarding process' },
  { name: 'Purchase Request', id: 'purchase-request', steps: 4, avgTime: '2-3 days', category: 'procurement', complexity: 'medium', description: 'Request approval for purchasing goods or services' },
  { name: 'Contract Renewal', id: 'contract-renewal', steps: 6, avgTime: '2-3 weeks', category: 'legal', complexity: 'medium', description: 'Review and renew existing contracts' },
  
  // Financial ERP Workflows
  { name: 'Simple Journal Entry', id: 'gl-journal-entry-simple', steps: 2, avgTime: '30 minutes', category: 'general_ledger', complexity: 'low', description: 'Basic journal entry with standard debit/credit validation' },
  { name: 'Vendor Invoice Approval', id: 'ap-vendor-invoice-approval', steps: 3, avgTime: '3-5 days', category: 'accounts_payable', complexity: 'medium', description: 'Multi-level approval process for vendor invoices' },
  { name: 'Customer Invoice Creation', id: 'ar-customer-invoice-creation', steps: 2, avgTime: '1-2 days', category: 'accounts_receivable', complexity: 'medium', description: 'Create and process customer invoices' },
  { name: 'Cash Flow Forecast', id: 'cf-cash-forecast-approval', steps: 2, avgTime: '2-3 days', category: 'cash_flow', complexity: 'medium', description: 'Create and approve monthly cash flow forecasts' },
  { name: 'Department Budget Approval', id: 'budget-department-budget-approval', steps: 3, avgTime: '3-4 weeks', category: 'budgeting', complexity: 'high', description: 'Annual department budget planning and approval' },
  { name: 'Asset Acquisition', id: 'fa-asset-acquisition-approval', steps: 3, avgTime: '1-2 weeks', category: 'fixed_assets', complexity: 'medium', description: 'Approval process for acquiring fixed assets' },
  { name: 'Quarterly Tax Filing', id: 'tax-quarterly-filing-preparation', steps: 3, avgTime: '1 week', category: 'tax_management', complexity: 'high', description: 'Prepare and file quarterly tax returns' },
  { name: 'Monthly Reconciliation', id: 'compliance-monthly-reconciliation', steps: 2, avgTime: '3-5 days', category: 'compliance', complexity: 'medium', description: 'Monthly account reconciliation and compliance review' },
  { name: 'Financial Statements', id: 'report-financial-statements', steps: 3, avgTime: '1-2 weeks', category: 'reporting', complexity: 'high', description: 'Prepare and review monthly financial statements' }
];

// ===== STEP DEFINITIONS (Lazy loaded when needed) =====
const STEP_DEFINITIONS = {
  'vendor-onboarding': () => import('.//steps/vendorOnboardingSteps.js'),
  'employee-onboarding': () => import('./steps/employeeOnboardingSteps.js'),
  'purchase-request': () => import('./steps/purchaseRequestSteps.js'),
  'contract-renewal': () => import('./steps/contractRenewalSteps.js'),
  'gl-journal-entry-simple': () => import('./steps/journalEntrySteps.js'),
  'ap-vendor-invoice-approval': () => import('./steps/invoiceApprovalSteps.js'),
  'ar-customer-invoice-creation': () => import('./steps/customerInvoiceSteps.js'),
  'cf-cash-forecast-approval': () => import('./steps/cashFlowSteps.js'),
  'budget-department-budget-approval': () => import('./steps/budgetApprovalSteps.js'),
  'fa-asset-acquisition-approval': () => import('./steps/assetAcquisitionSteps.js'),
  'tax-quarterly-filing-preparation': () => import('./steps/taxFilingSteps.js'),
  'compliance-monthly-reconciliation': () => import('./steps/reconciliationSteps.js'),
  'report-financial-statements': () => import('./steps/financialStatementsSteps.js')
};

// ===== ENHANCED TEMPLATES WITH STEP LOADING =====
function enhanceTemplateWithSteps(template) {
  return {
    ...template,
    estimated_duration: template.avgTime,
    approval_levels: template.complexity === 'high' ? 3 : template.complexity === 'medium' ? 2 : 0,
    module: template.category.split('_')[0],
    version: '1.0.8',
    
    // Lazy load detailed steps when needed
    async getSteps() {
      if (STEP_DEFINITIONS[template.id]) {
        try {
          const stepModule = await STEP_DEFINITIONS[template.id]();
          return stepModule.default || stepModule.steps;
        } catch (error) {
          console.warn(`Failed to load steps for ${template.id}:`, error);
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

// ===== STEPTYPE =====
export const WORKFLOW_STEPTYPES = {
 'shared_entity_selection', 
 'form_submission', 
 'approval', 
 'checklist', 
 'agent', 
 'system',
 'submit_database' 
}







// ===== UTILITY FUNCTIONS =====
export function getAllWorkflowTemplates() {
  return BASIC_TEMPLATES.map(enhanceTemplateWithSteps);
}

export function getWorkflowTemplateById(id) {
  const template = BASIC_TEMPLATES.find(t => t.id === id);
  return template ? enhanceTemplateWithSteps(template) : null;
}

export function getWorkflowsByCategory(category) {
  return BASIC_TEMPLATES
    .filter(t => t.category === category)
    .map(enhanceTemplateWithSteps);
}

export default getAllWorkflowTemplates();