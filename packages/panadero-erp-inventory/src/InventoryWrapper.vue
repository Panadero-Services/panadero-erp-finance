<!--
  Inventory Wrapper Component
  @version 1.0.12
  @date 16-Sep-2025
  @description Wrapper component for ERP.inventory management
  @test-workflow Testing branch workflow system
-->
<script setup>
import { ref, computed, onMounted, onUnmounted, provide } from 'vue';
import { useInventoryStore } from './stores/inventoryStore';
import { useScaling } from 'panadero-shared-styling';
import InventoryLayout from './components/layout/InventoryLayout.vue';
import InfoBoard from './components/InfoBoard.vue';
import InventoryDashboard from './components/dashboard/InventoryDashboard.vue';
//import Products from './components/Products.vue';
import ProductsEnhanced from './pages/ProductsEnhanced.vue';
import StoragesTest from './pages/StoragesTest.vue';
import StockLevels from './components/StockLevels.vue';
import Incoming from './components/Incoming.vue';
import Outgoing from './components/Outgoing.vue';
import Vendors from './components/Vendors.vue';
import Closeout from './components/Closeout.vue';
import InventoryReporting from './components/InventoryReporting.vue';
import ErpOrders from './components/ErpOrders.vue';
import AgentPortal from './components/AgentPortal.vue';

// Import the Framework Settings Panel from shared components package
import { FrameworkSettingsPanel } from 'panadero-shared-components';

// Props for settings from parent
const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
});

// Provide settings store to all child components - this IS the SSOT
provide('settingsStore', props.settings);

const store = useInventoryStore();
const { scalingStyles } = useScaling();

const tabs = [
  { id: 'infoboard', label: 'ERP Inventory', icon: 'fas fa-info-circle', color: 'text-blue-500', component: InfoBoard },
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', color: 'text-indigo-500', component: InventoryDashboard },
  //{ id: 'products', label: 'Products', icon: 'fas fa-box', color: 'text-green-500', component: Products },
  { id: 'products-enhanced', label: 'Products Enhanced', icon: 'fas fa-cube', color: 'text-green-600', component: ProductsEnhanced },
  { id: 'storages', label: 'Storages', icon: 'fas fa-warehouse', color: 'text-orange-500', component: StoragesTest },
  { id: 'stock-levels', label: 'Stock Levels', icon: 'fas fa-boxes', color: 'text-emerald-500', component: StockLevels },
  { id: 'erp-orders', label: 'ERP Orders', icon: 'fas fa-shopping-cart', color: 'text-yellow-500', component: ErpOrders },
  { id: 'incoming', label: 'Incoming', icon: 'fas fa-arrow-down', color: 'text-orange-500', component: Incoming },
  { id: 'outgoing', label: 'Outgoing', icon: 'fas fa-arrow-up', color: 'text-red-500', component: Outgoing },
  { id: 'vendors', label: 'Vendors & Partners', icon: 'fas fa-handshake', color: 'text-purple-500', component: Vendors },
  { id: 'closeout', label: 'Closeout Reports', icon: 'fas fa-chart-line', color: 'text-cyan-500', component: Closeout },
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
  <InventoryLayout 
    :active-tab="activeTab" 
    :tabs="tabs" 
    @tab-change="activeTab = $event"
  >
    <component :is="activeComponent" @tab-change="activeTab = $event" />
  </InventoryLayout>

  <FrameworkSettingsPanel :settings="settings" />
</template>