// ERP Finance Module - Main Entry Point
export { default as FinanceWrapper } from './src/FinanceWrapper.vue';
export { default as FinanceSettingsPanel } from './src/FinanceSettingsPanel.vue';
export { default as StatusBadge } from './src/components/ui/StatusBadge.vue';
export { default as ScaledIcon } from './src/components/ui/ScaledIcon.vue';
export { default as FinanceToggle } from './src/components/ui/FinanceToggle.vue';
export { default as FinanceButton } from './src/components/ui/FinanceButton.vue';
export { default as FinanceDropdown } from './src/components/ui/FinanceDropdown.vue';
export { default as FinanceValueCard } from './src/components/ui/FinanceValueCard.vue';
export { default as FinanceToggleDemo } from './src/components/demo/FinanceToggleDemo.vue';
export { default as FinanceButtonDemo } from './src/components/demo/FinanceButtonDemo.vue';
export { default as FinanceDropdownDemo } from './src/components/demo/FinanceDropdownDemo.vue';
export { default as StatusBadgeDemo } from './src/components/demo/StatusBadgeDemo.vue';
export { default as FinanceValueCardDemo } from './src/components/demo/FinanceValueCardDemo.vue';
export { default as FinanceDashboard } from './src/components/dashboard/FinanceDashboard.vue';
export { default as FinanceLayout } from './src/components/layout/FinanceLayout.vue';
export { default as FinanceNavigation } from './src/components/navigation/FinanceNavigation.vue';
export { default as GeneralLedger } from './src/components/GeneralLedger.vue';
export { default as AccountsPayable } from './src/components/AccountsPayable.vue';
export { default as AccountsReceivable } from './src/components/AccountsReceivable.vue';
export { default as CashFlow } from './src/components/CashFlow.vue';
export { default as TaxManagement } from './src/components/TaxManagement.vue';

// Export workflow package components
export { 
  WorkflowDashboard, 
  WorkflowDemo, 
  WorkflowManager,
  WorkflowCard,
  WorkflowForm,
  WorkflowModal,
  WorkflowStepper,
  WorkflowInfo,
  WorkflowStatistics,
  ActiveWorkflowCard,
  WorkflowTemplate,
  WorkflowApproval,
  WorkflowChecklist,
  WorkflowEntitySelection,
  WorkflowDatabaseSubmit,
  ModalContentWrapper,
  useWorkflowStore
} from './src/packages/panadero-workflow/index.js';

// Export composables
export { useGeneralLedger } from './src/composables/useGeneralLedger.js';
export { useAccountsPayable } from './src/composables/useAccountsPayable.js';
export { useAccountsReceivable } from './src/composables/useAccountsReceivable.js';
export { useCashFlow } from './src/composables/useCashFlow.js';
export { useTaxManagement } from './src/composables/useTaxManagement.js';
export { useInfoBoxes } from './src/composables/useInfoBoxes.js';
export { useInvoiceSystem } from './src/composables/useInvoiceSystem.js';
export { useInvoiceApi } from './src/composables/useInvoiceApi.js';
export { darkMode, darkModeUtils, darkModeClasses } from './src/utils/darkMode.js';
export { useStyling } from './src/composables/useStyling.js';

// Export store
export { useFinanceStore } from './src/stores/financeStore.js';

// Package information
export const packageInfo = {
  name: 'panadero-erp-finance',
  version: '1.10.0',
  description: 'ERP Finance Module for comprehensive financial management with modular workflow integration',
  author: 'JaWsome.Orbit',
  components: [
    'Finance',
    'GeneralLedger', 
    'AccountsPayable',
    'AccountsReceivable',
    'CashFlow',
    'TaxManagement',
    'WorkflowDashboard',
    'WorkflowDemo',
    'WorkflowManager'
  ]
};