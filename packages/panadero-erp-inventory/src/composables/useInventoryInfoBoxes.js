import { ref, computed } from 'vue'
import { useInventoryStore } from '../stores/inventoryStore.js'

export function useInventoryInfoBoxes() {
  const store = useInventoryStore()

  const infoBoxes = ref([
    {
      id: 'inventory-overview',
      title: 'Inventory Overview',
      description: 'Complete inventory management system with stock tracking, warehousing, and supply chain management',
      icon: 'fas fa-boxes',
      color: 'blue',
      stats: [
        { label: 'Total Items', value: computed(() => store.stockItems.length) },
        { label: 'Low Stock Items', value: computed(() => store.getLowStockItems.length) },
        { label: 'Total Value', value: computed(() => `$${store.getTotalInventoryValue.toLocaleString()}`) },
        { label: 'Active Warehouses', value: computed(() => store.warehouses.filter(w => w.status === 'active').length) }
      ]
    },
    {
      id: 'stock-management',
      title: 'Stock Management',
      description: 'Track inventory levels, manage stock movements, and monitor item status',
      icon: 'fas fa-warehouse',
      color: 'green',
      features: [
        'Real-time stock tracking',
        'Low stock alerts',
        'Stock movement history',
        'Multi-location support'
      ]
    },
    {
      id: 'warehouse-management',
      title: 'Warehouse Management',
      description: 'Manage warehouse locations, capacity, and organization',
      icon: 'fas fa-building',
      color: 'purple',
      features: [
        'Multi-warehouse support',
        'Capacity monitoring',
        'Zone management',
        'Location tracking'
      ]
    },
    {
      id: 'purchase-orders',
      title: 'Purchase Orders',
      description: 'Create and manage purchase orders with supplier integration',
      icon: 'fas fa-shopping-cart',
      color: 'orange',
      features: [
        'PO creation and approval',
        'Supplier integration',
        'Delivery tracking',
        'Cost management'
      ]
    },
    {
      id: 'supplier-management',
      title: 'Supplier Management',
      description: 'Manage supplier relationships and performance tracking',
      icon: 'fas fa-truck',
      color: 'cyan',
      features: [
        'Supplier database',
        'Performance tracking',
        'Contact management',
        'Rating system'
      ]
    },
    {
      id: 'inventory-reports',
      title: 'Inventory Reports',
      description: 'Generate comprehensive reports and analytics',
      icon: 'fas fa-chart-bar',
      color: 'pink',
      features: [
        'Stock level reports',
        'Movement analysis',
        'Value reports',
        'Custom reporting'
      ]
    },
    {
      id: 'agent-portal',
      title: 'AI Agent Portal',
      description: 'Leverage AI for inventory optimization and insights',
      icon: 'fas fa-robot',
      color: 'teal',
      features: [
        'AI-powered analysis',
        'Custom prompts',
        'Optimization suggestions',
        'Predictive insights'
      ]
    }
  ])

  const getInfoBox = (id) => {
    return infoBoxes.value.find(box => box.id === id)
  }

  const getInfoBoxesByCategory = (category) => {
    return infoBoxes.value.filter(box => box.category === category)
  }

  return {
    infoBoxes,
    getInfoBox,
    getInfoBoxesByCategory
  }
}
