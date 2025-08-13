import { ref, computed } from 'vue';

export function useInfoBoxes() {
  const activeInfo = ref(1);

  // Package tables data
  const packageTables = [
    { name: 'finance_accounts', records: 6, lastUpdated: '3 days ago' },
    { name: 'finance_journal_entries', records: 12, lastUpdated: '1 day ago' },
    { name: 'finance_payables', records: 8, lastUpdated: '2 days ago' },
    { name: 'finance_receivables', records: 15, lastUpdated: '1 day ago' },
    { name: 'finance_tax_records', records: 4, lastUpdated: '5 days ago' },
    { name: 'finance_cashflow_transactions', records: 22, lastUpdated: 'today' }
  ];

  // Shared entities data
  const sharedEntities = [
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
  ];

  // Version updates data
  const versionUpdates = [
    {
      version: 'v1.01',
      date: '11-aug-2025',
      features: [
        'Dashboard overview (KPIs, recent entries, overdue AP/AR, cash by category)',
        'Fixed Assets (register assets, straight-line depreciation posted to GL)',
        'Reporting (Income Statement, Balance Sheet, Cash Flow) with CSV export',
        'Budgeting & Forecasting (budgets, variance vs actual per account)',
        'Compliance & Audit (audit logs, basic segregation-of-duties check)',
        'API ready: endpoints for assets, budgets, audit; seeders for accounts/periods/categories',
        'Improved GL validation and period close (debits must equal credits)'
      ]
    },
    {
      version: 'v1.02',
      date: '13-aug-2025',
      features: [
        'Framework Shared Entities',
        'Enhanced Dependencies section with clickable navigation',
        'Improved 2-column grid layout for better space utilization'
      ]
    }
  ];

  // Module description
  const moduleDescription = {
    title: 'ERP Finance Module v1.0.1',
    description: 'This module provides comprehensive financial management capabilities including:',
    features: [
      'General Ledger with journal entries and trial balance',
      'Accounts Payable with vendor management and aging analysis',
      'Accounts Receivable with customer management and aging analysis',
      'Cash Flow tracking with categorization',
      'Tax Management with compliance tracking'
    ]
  };

  // Navigation function for shared entities
  function navigateToEntity(entityType) {
    const routes = {
      'business_services': '/home/business_services',
      'projects': '/project/projects',
      'users': '/admin/users',
      'settings': '/config/settings',
      'vendors': '/erp/vendors',
      'customers': '/erp/customers'
    };
    
    const route = routes[entityType];
    if (route) {
      // Use Inertia router if available, otherwise window.location
      if (window.Inertia) {
        window.Inertia.visit(route);
      } else {
        window.location.href = route;
      }
    }
  }

  // Toggle info box
  function toggleInfoBox() {
    activeInfo.value = !activeInfo.value;
  }

  // Get color classes for shared entities
  function getEntityColorClasses(color) {
    const colorMap = {
      blue: 'bg-blue-50 hover:bg-blue-100 text-blue-700 text-blue-600 text-blue-500',
      green: 'bg-green-50 hover:bg-green-100 text-green-700 text-green-600 text-green-500',
      purple: 'bg-purple-50 hover:bg-purple-100 text-purple-700 text-purple-600 text-purple-500',
      orange: 'bg-orange-50 hover:bg-orange-100 text-orange-700 text-orange-600 text-orange-500',
      teal: 'bg-teal-50 hover:bg-teal-100 text-teal-700 text-teal-600 text-teal-500',
      indigo: 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-indigo-600 text-indigo-500'
    };
    
    return colorMap[color] || colorMap.blue;
  }

  return {
    // State
    activeInfo,
    
    // Data
    packageTables,
    sharedEntities,
    versionUpdates,
    moduleDescription,
    
    // Functions
    navigateToEntity,
    toggleInfoBox,
    getEntityColorClasses
  };
}
