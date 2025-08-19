/**
 * Financial ERP Workflow Templates Configuration
 * 
 * This file provides a comprehensive mapping of all financial workflow templates
 * organized by module and category for easy access and management.
 */

export const FINANCIAL_WORKFLOW_TEMPLATES = {
  // ===== GENERAL LEDGER MODULE =====
  general_ledger: {
    name: 'General Ledger',
    description: 'Core accounting and journal entry workflows',
    templates: [
      {
        id: 'gl-journal-entry-simple',
        name: 'Simple Journal Entry',
        description: 'Basic journal entry with standard debit/credit validation',
        complexity: 'low',
        estimated_duration: '30 minutes',
        approval_levels: 0,
        category: 'daily_operations'
      },
      {
        id: 'gl-journal-entry-complex',
        name: 'Complex Journal Entry',
        description: 'Multi-line journal entries with advanced validation',
        complexity: 'medium',
        estimated_duration: '1 hour',
        approval_levels: 1,
        category: 'monthly_close'
      },
      {
        id: 'gl-period-close',
        name: 'Period Close Process',
        description: 'Monthly/quarterly period close with adjustments',
        complexity: 'high',
        estimated_duration: '1-2 days',
        approval_levels: 2,
        category: 'period_close'
      }
    ]
  },

  // ===== ACCOUNTS PAYABLE MODULE =====
  accounts_payable: {
    name: 'Accounts Payable',
    description: 'Vendor management and payment workflows',
    templates: [
      {
        id: 'ap-vendor-invoice-approval',
        name: 'Vendor Invoice Approval',
        description: 'Multi-level approval process for vendor invoices',
        complexity: 'medium',
        estimated_duration: '3-5 business days',
        approval_levels: 2,
        category: 'invoice_processing'
      },
      {
        id: 'ap-vendor-setup',
        name: 'Vendor Setup & Onboarding',
        description: 'New vendor registration and validation',
        complexity: 'medium',
        estimated_duration: '1-2 weeks',
        approval_levels: 2,
        category: 'vendor_management'
      },
      {
        id: 'ap-payment-processing',
        name: 'Payment Processing',
        description: 'Vendor payment approval and processing',
        complexity: 'low',
        estimated_duration: '1-2 business days',
        approval_levels: 1,
        category: 'payment_processing'
      }
    ]
  },

  // ===== ACCOUNTS RECEIVABLE MODULE =====
  accounts_receivable: {
    name: 'Accounts Receivable',
    description: 'Customer billing and collection workflows',
    templates: [
      {
        id: 'ar-customer-invoice-creation',
        name: 'Customer Invoice Creation',
        description: 'Create and approve customer invoices with validation',
        complexity: 'low',
        estimated_duration: '1-2 business days',
        approval_levels: 1,
        category: 'billing'
      },
      {
        id: 'ar-collection-process',
        name: 'Collection Process',
        description: 'Customer payment collection and follow-up',
        complexity: 'medium',
        estimated_duration: 'ongoing',
        approval_levels: 0,
        category: 'collections'
      },
      {
        id: 'ar-credit-memo',
        name: 'Credit Memo Processing',
        description: 'Customer credit and refund processing',
        complexity: 'medium',
        estimated_duration: '2-3 business days',
        approval_levels: 2,
        category: 'customer_service'
      }
    ]
  },

  // ===== CASH FLOW MODULE =====
  cash_flow: {
    name: 'Cash Flow Management',
    description: 'Cash flow forecasting and management workflows',
    templates: [
      {
        id: 'cf-cash-forecast-approval',
        name: 'Cash Flow Forecast Approval',
        description: 'Monthly cash flow forecasting and approval process',
        complexity: 'medium',
        estimated_duration: '2-3 business days',
        approval_levels: 1,
        category: 'forecasting'
      },
      {
        id: 'cf-cash-position-daily',
        name: 'Daily Cash Position',
        description: 'Daily cash position monitoring and reporting',
        complexity: 'low',
        estimated_duration: '1 hour',
        approval_levels: 0,
        category: 'daily_monitoring'
      },
      {
        id: 'cf-investment-approval',
        name: 'Investment Approval',
        description: 'Short-term investment decisions and approvals',
        complexity: 'high',
        estimated_duration: '1-2 weeks',
        approval_levels: 3,
        category: 'treasury'
      }
    ]
  },

  // ===== BUDGETING MODULE =====
  budgeting: {
    name: 'Budgeting & Forecasting',
    description: 'Budget creation, approval, and variance analysis workflows',
    templates: [
      {
        id: 'budget-department-budget-approval',
        name: 'Department Budget Approval',
        description: 'Annual department budget creation and approval',
        complexity: 'high',
        estimated_duration: '3-4 weeks',
        approval_levels: 3,
        category: 'annual_planning'
      },
      {
        id: 'budget-variance-analysis',
        name: 'Budget Variance Analysis',
        description: 'Monthly budget variance analysis and reporting',
        complexity: 'medium',
        estimated_duration: '1 week',
        approval_levels: 1,
        category: 'monthly_review'
      },
      {
        id: 'budget-forecast-update',
        name: 'Budget Forecast Update',
        description: 'Quarterly budget forecast updates and revisions',
        complexity: 'medium',
        estimated_duration: '2 weeks',
        approval_levels: 2,
        category: 'quarterly_planning'
      }
    ]
  },

  // ===== FIXED ASSETS MODULE =====
  fixed_assets: {
    name: 'Fixed Assets',
    description: 'Asset acquisition, depreciation, and disposal workflows',
    templates: [
      {
        id: 'fa-asset-acquisition-approval',
        name: 'Asset Acquisition Approval',
        description: 'Capital asset purchase approval and setup process',
        complexity: 'high',
        estimated_duration: '1-2 weeks',
        approval_levels: 3,
        category: 'acquisition'
      },
      {
        id: 'fa-depreciation-calculation',
        name: 'Depreciation Calculation',
        description: 'Monthly depreciation calculation and posting',
        complexity: 'low',
        estimated_duration: '1 day',
        approval_levels: 0,
        category: 'monthly_operations'
      },
      {
        id: 'fa-asset-disposal',
        name: 'Asset Disposal Process',
        description: 'Asset disposal approval and accounting',
        complexity: 'medium',
        estimated_duration: '1 week',
        approval_levels: 2,
        category: 'asset_lifecycle'
      }
    ]
  },

  // ===== TAX MANAGEMENT MODULE =====
  tax_management: {
    name: 'Tax Management',
    description: 'Tax calculation, filing, and compliance workflows',
    templates: [
      {
        id: 'tax-quarterly-filing-preparation',
        name: 'Quarterly Tax Filing Preparation',
        description: 'Quarterly tax calculation and filing preparation',
        complexity: 'high',
        estimated_duration: '1 week',
        approval_levels: 2,
        category: 'quarterly_filing'
      },
      {
        id: 'tax-annual-return',
        name: 'Annual Tax Return',
        description: 'Annual tax return preparation and filing',
        complexity: 'high',
        estimated_duration: '2-3 weeks',
        approval_levels: 3,
        category: 'annual_filing'
      },
      {
        id: 'tax-compliance-review',
        name: 'Tax Compliance Review',
        description: 'Tax compliance monitoring and review',
        complexity: 'medium',
        estimated_duration: '1 week',
        approval_levels: 1,
        category: 'compliance'
      }
    ]
  },

  // ===== COMPLIANCE & AUDIT MODULE =====
  compliance_audit: {
    name: 'Compliance & Audit',
    description: 'Regulatory compliance and audit trail workflows',
    templates: [
      {
        id: 'compliance-monthly-reconciliation',
        name: 'Monthly Account Reconciliation',
        description: 'Monthly bank and account reconciliation process',
        complexity: 'medium',
        estimated_duration: '3-5 business days',
        approval_levels: 1,
        category: 'monthly_close'
      },
      {
        id: 'compliance-internal-audit',
        name: 'Internal Audit Process',
        description: 'Internal audit planning and execution',
        complexity: 'high',
        estimated_duration: '2-4 weeks',
        approval_levels: 2,
        category: 'audit'
      },
      {
        id: 'compliance-regulatory-filing',
        name: 'Regulatory Filing',
        description: 'Regulatory report preparation and filing',
        complexity: 'high',
        estimated_duration: '1-2 weeks',
        approval_levels: 2,
        category: 'regulatory'
      }
    ]
  },

  // ===== REPORTING MODULE =====
  reporting: {
    name: 'Financial Reporting',
    description: 'Financial statement and report preparation workflows',
    templates: [
      {
        id: 'report-financial-statements',
        name: 'Financial Statement Preparation',
        description: 'Monthly/quarterly financial statement preparation process',
        complexity: 'high',
        estimated_duration: '1-2 weeks',
        approval_levels: 2,
        category: 'monthly_close'
      },
      {
        id: 'report-management-reporting',
        name: 'Management Reporting',
        description: 'Management dashboard and KPI reporting',
        complexity: 'medium',
        estimated_duration: '1 week',
        approval_levels: 1,
        category: 'management'
      },
      {
        id: 'report-external-reporting',
        name: 'External Reporting',
        description: 'External stakeholder and regulatory reporting',
        complexity: 'high',
        estimated_duration: '2-3 weeks',
        approval_levels: 3,
        category: 'external'
      }
    ]
  }
};

// ===== WORKFLOW COMPLEXITY LEVELS =====
export const WORKFLOW_COMPLEXITY = {
  low: {
    name: 'Low Complexity',
    description: 'Simple workflows with minimal steps and no approvals',
    color: 'green',
    icon: 'check-circle'
  },
  medium: {
    name: 'Medium Complexity',
    description: 'Moderate workflows with multiple steps and some approvals',
    color: 'yellow',
    icon: 'clock'
  },
  high: {
    name: 'High Complexity',
    description: 'Complex workflows with multiple steps and approvals',
    color: 'red',
    icon: 'alert-triangle'
  }
};

// ===== WORKFLOW CATEGORIES =====
export const WORKFLOW_CATEGORIES = {
  daily_operations: 'Daily Operations',
  monthly_close: 'Monthly Close',
  quarterly_planning: 'Quarterly Planning',
  annual_planning: 'Annual Planning',
  invoice_processing: 'Invoice Processing',
  vendor_management: 'Vendor Management',
  payment_processing: 'Payment Processing',
  billing: 'Billing',
  collections: 'Collections',
  customer_service: 'Customer Service',
  forecasting: 'Forecasting',
  daily_monitoring: 'Daily Monitoring',
  treasury: 'Treasury',
  monthly_review: 'Monthly Review',
  acquisition: 'Acquisition',
  monthly_operations: 'Monthly Operations',
  asset_lifecycle: 'Asset Lifecycle',
  quarterly_filing: 'Quarterly Filing',
  annual_filing: 'Annual Filing',
  compliance: 'Compliance',
  audit: 'Audit',
  regulatory: 'Regulatory',
  management: 'Management',
  external: 'External'
};

// ===== UTILITY FUNCTIONS =====
export function getWorkflowTemplateById(templateId) {
  for (const module of Object.values(FINANCIAL_WORKFLOW_TEMPLATES)) {
    const template = module.templates.find(t => t.id === templateId);
    if (template) {
      return { ...template, module: module.name };
    }
  }
  return null;
}

export function getWorkflowsByModule(moduleKey) {
  return FINANCIAL_WORKFLOW_TEMPLATES[moduleKey] || null;
}

export function getWorkflowsByCategory(category) {
  const workflows = [];
  for (const module of Object.values(FINANCIAL_WORKFLOW_TEMPLATES)) {
    const filtered = module.templates.filter(t => t.category === category);
    workflows.push(...filtered.map(t => ({ ...t, module: module.name })));
  }
  return workflows;
}

export function getWorkflowsByComplexity(complexity) {
  const workflows = [];
  for (const module of Object.values(FINANCIAL_WORKFLOW_TEMPLATES)) {
    const filtered = module.templates.filter(t => t.complexity === complexity);
    workflows.push(...filtered.map(t => ({ ...t, module: module.name })));
  }
  return workflows;
}

export function getAllWorkflowTemplates() {
  const allTemplates = [];
  for (const module of Object.values(FINANCIAL_WORKFLOW_TEMPLATES)) {
    allTemplates.push(...module.templates.map(t => ({ ...t, module: module.name })));
  }
  return allTemplates;
}

export default FINANCIAL_WORKFLOW_TEMPLATES;
