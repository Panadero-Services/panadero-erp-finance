import { ref, computed } from 'vue';
import { useFinanceStore } from '../stores/financeStore';
import versionsData from '../data/versions.json';

export function useInfoBoxes() {
  const activeInfo = ref(1);
  const store = useFinanceStore();

  // Dynamic package tables data computed from store
  const packageTables = computed(() => {
    const now = new Date();
    
    return [
      {
        name: 'finance_accounts',
        records: Object.keys(store.accountBalances).length,
        lastUpdated: getLastUpdated(store.journalEntries, 'account updates')
      },
      {
        name: 'finance_journal_entries',
        records: store.journalEntries.length,
        lastUpdated: getLastUpdated(store.journalEntries, 'journal entries')
      },
      {
        name: 'finance_payables',
        records: store.payables.length,
        lastUpdated: getLastUpdated(store.payables, 'payables')
      },
      {
        name: 'finance_receivables',
        records: store.receivables.length,
        lastUpdated: getLastUpdated(store.receivables, 'receivables')
      },
      {
        name: 'finance_tax_records',
        records: store.taxRecords.length,
        lastUpdated: getLastUpdated(store.taxRecords, 'tax records')
      },
      {
        name: 'finance_cashflow_transactions',
        records: store.cashFlowTransactions.length,
        lastUpdated: getLastUpdated(store.cashFlowTransactions, 'cash flow')
      },
      {
        name: 'finance_fixed_assets',
        records: store.fixedAssets.length,
        lastUpdated: getLastUpdated(store.fixedAssets, 'fixed assets')
      },
      {
        name: 'finance_budgets',
        records: store.budgets.length,
        lastUpdated: getLastUpdated(store.budgets, 'budgets')
      },
      {
        name: 'finance_audit_logs',
        records: store.auditLogs.length,
        lastUpdated: getLastUpdated(store.auditLogs, 'audit logs')
      }
    ];
  });

  // Total records count for module description
  const totalRecords = computed(() => {
    return Object.keys(store.accountBalances).length +
           store.journalEntries.length +
           store.payables.length +
           store.receivables.length +
           store.taxRecords.length +
           store.cashFlowTransactions.length +
           store.fixedAssets.length +
           store.budgets.length +
           store.auditLogs.length;
  });

  // Helper function to calculate last updated time
  function getLastUpdated(dataArray, fallbackText) {
    if (!dataArray || dataArray.length === 0) {
      return 'never';
    }
    
    // Find the most recent timestamp
    const timestamps = dataArray
      .map(item => {
        // Handle different timestamp fields
        const timestamp = item.timestamp || item.invoice_date || item.transaction_date || item.acquired_at;
        return timestamp ? new Date(timestamp) : null;
      })
      .filter(date => date && !isNaN(date.getTime()));
    
    if (timestamps.length === 0) {
      return 'never';
    }
    
    const mostRecent = new Date(Math.max(...timestamps));
    const now = new Date();
    const diffMs = now - mostRecent;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'today';
    if (diffDays === 1) return '1 day ago';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
    return `${Math.floor(diffDays / 365)} years ago`;
  }

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

  // ✅ WORKING: Dynamic version from JSON
  const versionUpdates = computed(() => versionsData.releases);

  // ✅ WORKING: Dynamic current version
  const moduleDescription = computed(() => ({
    title: `ERP Finance Module v${versionsData.current} (${totalRecords.value} records)`,
    version: `v${versionsData.current}`,
    description: 'Comprehensive financial management system with complete workflow management, 4-column modal interface, and 7 documented workflow step types.',
    totalRecords: totalRecords.value,
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
    ],
    features: [
      'FinanceDropdown component with perfect height matching to FinanceButton',
      'Auto-scaling integration through store.fontSizes.base system',
      'Enhanced FinanceButton with automatic font and padding scaling',
      'Modernized GeneralLedger with consistent dropdown components',
      'Transparent outline variants with proper CSS specificity',
      'Interactive component demos in InfoBoard section',
      'Font Awesome icon integration with proper loading',
      'Professional dark mode support across all components',
      'Centralized StatusBadge and ScaledIcon components',
      'Complete DRY refactoring with centralized styling system'
    ]
  }));

  // Navigation function for shared entities and package tables
  function navigateToEntity(entityType) {
    const routes = {
      // Framework shared entities
      'business_services': '/home/business_services',
      'projects': '/project/projects',
      'users': '/admin/users',
      'settings': '/config/settings',
      'vendors': '/erp/vendors',
      'customers': '/erp/customers',
      
      // Finance package tables - use the actual tab IDs from Finance.vue
      'finance_accounts': '#general-ledger',
      'finance_journal_entries': '#general-ledger',
      'finance_payables': '#accounts-payable',
      'finance_receivables': '#accounts-receivable',
      'finance_tax_records': '#tax-management',
      'finance_cashflow_transactions': '#cash-flow',
      'finance_fixed_assets': '#fixed-assets',
      'finance_budgets': '#budgeting',
      'finance_audit_logs': '#compliance'
    };
    
    const route = routes[entityType];
    if (route) {
      if (route.startsWith('#')) {
        // Handle hash-based navigation for finance tabs
        const tabId = route.substring(1);
        // Emit a custom event that the Finance.vue component can listen to
        window.dispatchEvent(new CustomEvent('switchFinanceTab', { 
          detail: { tabId } 
        }));
      } else {
        // Use Inertia router if available, otherwise window.location
        if (window.Inertia) {
          window.Inertia.visit(route);
        } else {
          window.location.href = route;
        }
      }
    }
  }

  // Toggle info box
  function toggleInfoBox() {
    activeInfo.value = !activeInfo.value;
  }

  // Get color classes for shared entities and package tables
  function getEntityColorClasses(color) {
    const colorMap = {
      // Framework entity colors
      blue: 'bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 dark:text-blue-300 dark:hover:text-blue-200',
      green: 'bg-green-50 hover:bg-green-100 text-green-700 dark:bg-green-900/20 dark:hover:bg-green-900/30 dark:text-green-300 dark:hover:text-green-200',
      purple: 'bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:hover:bg-purple-900/30 dark:text-purple-300 dark:hover:text-purple-200',
      orange: 'bg-orange-50 hover:bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:hover:bg-orange-900/30 dark:text-orange-300 dark:hover:text-orange-200',
      teal: 'bg-teal-50 hover:bg-teal-100 text-teal-700 dark:bg-teal-900/20 dark:hover:bg-teal-900/30 dark:text-teal-300 dark:hover:text-teal-200',
      indigo: 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 dark:text-indigo-300 dark:hover:text-indigo-200',
      
      // Package table colors
      gray: 'bg-gray-50 hover:bg-gray-100 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-gray-200',
      red: 'bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-900/20 dark:hover:bg-red-900/30 dark:text-red-300 dark:hover:text-red-200',
      yellow: 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/30 dark:text-yellow-300 dark:hover:text-yellow-200'
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
    totalRecords,
    
    // Functions
    navigateToEntity,
    toggleInfoBox,
    getEntityColorClasses
  };
}
