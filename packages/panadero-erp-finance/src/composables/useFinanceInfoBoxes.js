import { useGenericInfoBoxes } from '../../../../shared/composables/useGenericInfoBoxes.js';
import { useFinanceStore } from '../stores/financeStore';
import versionsData from '../data/versions.json';

export function useFinanceInfoBoxes() {
  const store = useFinanceStore();
  
  const financeConfig = {
    store,
    packageName: 'finance',
    tablePrefix: 'finance_',
    title: 'ERP Finance Module',
    description: 'Comprehensive financial management system with complete workflow management, 4-column modal interface, and 7 documented workflow step types.',
    
    // Table configurations
    tableConfigs: [
      {
        name: 'finance_accounts',
        getRecords: (store) => Object.keys(store.accountBalances).length,
        getData: (store) => store.journalEntries,
        fallbackText: 'account updates'
      },
      {
        name: 'finance_journal_entries',
        getRecords: (store) => store.journalEntries.length,
        getData: (store) => store.journalEntries,
        fallbackText: 'journal entries'
      },
      {
        name: 'finance_payables',
        getRecords: (store) => store.payables.length,
        getData: (store) => store.payables,
        fallbackText: 'payables'
      },
      {
        name: 'finance_receivables',
        getRecords: (store) => store.receivables.length,
        getData: (store) => store.receivables,
        fallbackText: 'receivables'
      },
      {
        name: 'finance_tax_records',
        getRecords: (store) => store.taxRecords.length,
        getData: (store) => store.taxRecords,
        fallbackText: 'tax records'
      },
      {
        name: 'finance_cashflow_transactions',
        getRecords: (store) => store.cashFlowTransactions.length,
        getData: (store) => store.cashFlowTransactions,
        fallbackText: 'cash flow'
      },
      {
        name: 'finance_fixed_assets',
        getRecords: (store) => store.fixedAssets.length,
        getData: (store) => store.fixedAssets,
        fallbackText: 'fixed assets'
      },
      {
        name: 'finance_budgets',
        getRecords: (store) => store.budgets.length,
        getData: (store) => store.budgets,
        fallbackText: 'budgets'
      },
      {
        name: 'finance_audit_logs',
        getRecords: (store) => store.auditLogs.length,
        getData: (store) => store.auditLogs,
        fallbackText: 'audit logs'
      }
    ],
    
    // Shared entities configuration
    sharedEntitiesConfig: [
      {
        id: 'business_services',
        name: 'Business Services',
        path: '../home/business_services',
        color: 'blue',
        icon: 'fas fa-briefcase'
      },
      {
        id: 'projects',
        name: 'Projects',
        path: '../project/projects',
        color: 'green',
        icon: 'fas fa-project-diagram'
      },
      {
        id: 'users',
        name: 'Users & Teams',
        path: '../admin/users',
        color: 'purple',
        icon: 'fas fa-users'
      },
      {
        id: 'settings',
        name: 'System Settings',
        path: '../config/settings',
        color: 'orange',
        icon: 'fas fa-cog'
      },
      {
        id: 'vendors',
        name: 'Vendors',
        path: '../erp/vendors',
        color: 'teal',
        icon: 'fas fa-truck'
      },
      {
        id: 'customers',
        name: 'Customers',
        path: '../erp/customers',
        color: 'indigo',
        icon: 'fas fa-user-tie'
      }
    ],
    
    // Version data
    versionData,
    
    // Navigation routes
    navigationRoutes: {
      // Framework shared entities
      'business_services': '/home/business_services',
      'projects': '/project/projects',
      'users': '/admin/users',
      'settings': '/config/settings',
      'vendors': '/erp/vendors',
      'customers': '/erp/customers',
      
      // Finance package tables
      'finance_accounts': '#general-ledger',
      'finance_journal_entries': '#general-ledger',
      'finance_payables': '#accounts-payable',
      'finance_receivables': '#accounts-receivable',
      'finance_tax_records': '#tax-management',
      'finance_cashflow_transactions': '#cash-flow',
      'finance_fixed_assets': '#fixed-assets',
      'finance_budgets': '#budgeting',
      'finance_audit_logs': '#compliance'
    },
    
    // Module descriptions
    descriptions: [
      {
        id: 1,
        title: 'Core Components',
        count: 9,
        list: [
          'General Ledger - Complete accounting system',
          'Accounts Payable - Vendor payment management',
          'Accounts Receivable - Customer invoice tracking',
          'Cash Flow - Transaction monitoring and analysis',
          'Fixed Assets - Asset lifecycle management',
          'Tax Management - Tax calculation and filing',
          'Budgeting & Forecasting - Financial planning tools',
          'Compliance & Audit - Regulatory compliance tracking',
          'Reporting - Financial reports and analytics'
        ]
      },
      {
        id: 2,
        title: 'UI Components',
        count: 7,
        list: [
          'FinanceButton - Auto-scaling button component with variants',
          'FinanceDropdown - Perfect height-matching dropdown component',
          'FinanceInput - Text input component matching dropdown dimensions exactly',
          'FinanceValueCard - Flexible value display cards with auto-coloring',
          'FinanceToggle - Modern toggle switches with color variants',
          'StatusBadge - Centralized status indicator component',
          'ScaledIcon - Dynamic icon sizing system'
        ]
      },
      {
        id: 3,
        title: 'Features',
        count: 12,
        list: [
          'Complete Workflow Management System with WorkflowDashboard',
          'Direct workflow creation from template cards with 4-column modal',
          'Active workflow tracking with unique IDs and delete capabilities',
          '7 Workflow Step Types: User Action, Timer, API, Approval, Agent, System, External',
          'Enhanced 3-column demo layout for better visual organization',
          'Inline usage examples directly under each demo section',
          'Modular InfoBoard architecture with separate section components',
          'Interactive demo selection with icon-coded buttons',
          'Auto-scaling integration through store.fontSizes.base',
          'Perfect component height matching system',
          'Professional dark mode support across all components',
          'Dynamic table row and header height scaling'
        ]
      }
    ]
  };
  
  return useGenericInfoBoxes(financeConfig);
}
