<!--
  Inventory Wrapper Component
  @version 1.0.0
  @date 31-Aug-2025
  @description Wrapper component for ERP.inventory management
-->
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useInventoryStore } from './stores/inventoryStore';
import InventoryLayout from './components/layout/InventoryLayout.vue';
import InfoBoard from './components/InfoBoard.vue';
import InventoryDashboard from './components/dashboard/InventoryDashboard.vue';
import StockManagement from './components/StockManagement.vue';
import WarehouseManagement from './components/WarehouseManagement.vue';
import PurchaseOrders from './components/PurchaseOrders.vue';
import SupplierManagement from './components/SupplierManagement.vue';
import InventoryReporting from './components/InventoryReporting.vue';
import AgentPortal from './components/AgentPortal.vue';

// Import the Framework Settings Panel from shared location
import FrameworkSettingsPanel from '../../shared/components/FrameworkSettingsPanel.vue';

const store = useInventoryStore();

const tabs = [
  { id: 'infoboard', label: 'ERP Inventory', icon: 'fas fa-info-circle', color: 'text-blue-500', component: InfoBoard },
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', color: 'text-indigo-500', component: InventoryDashboard },
  { id: 'stock-management', label: 'Stock Management', icon: 'fas fa-boxes', color: 'text-emerald-500', component: StockManagement },
  { id: 'warehouse-management', label: 'Warehouse Management', icon: 'fas fa-warehouse', color: 'text-orange-500', component: WarehouseManagement },
  { id: 'purchase-orders', label: 'Purchase Orders', icon: 'fas fa-shopping-cart', color: 'text-purple-500', component: PurchaseOrders },
  { id: 'supplier-management', label: 'Supplier Management', icon: 'fas fa-truck', color: 'text-cyan-500', component: SupplierManagement },
  { id: 'inventory-reports', label: 'Inventory Reports', icon: 'fas fa-chart-bar', color: 'text-pink-500', component: InventoryReporting },
  { id: 'agent-portal', label: 'Agent Portal', icon: 'fas fa-robot', color: 'text-teal-500', component: AgentPortal }
];

const activeTab = ref('dashboard');

const activeComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.component : InventoryDashboard;
});

function handleTabSwitch(event) {
  const { tabId } = event.detail;
  if (tabs.find(t => t.id === tabId)) {
    activeTab.value = tabId;
  }
}

// Add the missing function
function handleSettingsChanged(change) {
  if (change.type === 'fontSize') {
    console.debug('Framework font size changed to:', change.value);
  } else if (change.type === 'settings') {
    console.debug('Framework settings changed:', change.value);
  }
}

onMounted(() => {
  window.addEventListener('switchInventoryTab', handleTabSwitch);
});

onUnmounted(() => {
  window.removeEventListener('switchInventoryTab', handleTabSwitch);
});
</script>

<template>
  <InventoryLayout :active-tab="activeTab" :tabs="tabs" @tab-change="activeTab = $event">
    <component :is="activeComponent" @tab-change="activeTab = $event" />
  </InventoryLayout>
  
  <!-- Framework Settings Panel (no circular reference) -->
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>
