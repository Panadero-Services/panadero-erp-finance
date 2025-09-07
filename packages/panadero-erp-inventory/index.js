// Panadero ERP Inventory Module
// Main entry point for the inventory management system

// Export main wrapper component
export { default as InventoryWrapper } from './src/InventoryWrapper.vue';

// Export individual components
export { default as InventoryDashboard } from './src/components/dashboard/InventoryDashboard.vue';
export { default as StockManagement } from './src/components/StockManagement.vue';
export { default as WarehouseManagement } from './src/components/WarehouseManagement.vue';
export { default as PurchaseOrders } from './src/components/PurchaseOrders.vue';
export { default as SupplierManagement } from './src/components/SupplierManagement.vue';
export { default as InventoryReporting } from './src/components/InventoryReporting.vue';
export { default as AgentPortal } from './src/components/AgentPortal.vue';
export { default as InfoBoard } from './src/components/InfoBoard.vue';

// Export layout components
export { default as InventoryLayout } from './src/components/layout/InventoryLayout.vue';
export { default as InventoryNavigation } from './src/components/navigation/InventoryNavigation.vue';

// Export UI components
export { default as InventoryButton } from './src/components/ui/InventoryButton.vue';
export { default as InventoryDropdown } from './src/components/ui/InventoryDropdown.vue';
export { default as InventoryInput } from './src/components/ui/InventoryInput.vue';
export { default as InventoryToggle } from './src/components/ui/InventoryToggle.vue';
export { default as InventoryValueCard } from './src/components/ui/InventoryValueCard.vue';
export { default as StatusBadge } from './src/components/ui/StatusBadge.vue';
export { default as ScaledIcon } from './src/components/ui/ScaledIcon.vue';

// Export composables
export { useInventoryStore } from './src/stores/inventoryStore.js';
export { useStockManagement } from './src/composables/useStockManagement.js';
export { useWarehouseManagement } from './src/composables/useWarehouseManagement.js';
export { usePurchaseOrders } from './src/composables/usePurchaseOrders.js';
export { useSupplierManagement } from './src/composables/useSupplierManagement.js';
export { useInventoryReporting } from './src/composables/useInventoryReporting.js';
export { useAgentPortal } from './src/composables/useAgentPortal.js';
export { useInventoryInfoBoxes } from './src/composables/useInventoryInfoBoxes.js';
export { useStyling } from './src/composables/useStyling.js';

// Export store
export { useInventoryStore } from './src/stores/inventoryStore.js';

// Package information
export const packageInfo = {
  name: 'panadero-erp-inventory',
  version: '1.0.0',
  description: 'ERP Inventory Module for comprehensive inventory management with modular workflow integration',
  author: 'JaWsome.Orbit',
  components: [
    'Inventory',
    'StockManagement', 
    'WarehouseManagement',
    'PurchaseOrders',
    'SupplierManagement',
    'InventoryReporting',
    'AgentPortal',
    'WorkflowDashboard',
    'WorkflowDemo',
    'WorkflowManager'
  ]
};
