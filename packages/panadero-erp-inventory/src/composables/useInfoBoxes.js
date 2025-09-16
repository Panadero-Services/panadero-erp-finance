import { ref, computed } from 'vue';
import { useInventoryStore } from '../stores/inventoryStore';
import versionsData from '../data/versions.json';

export function useInfoBoxes() {
  const activeInfo = ref(1);
  const store = useInventoryStore();

  // Dynamic package tables data computed from store
  const packageTables = computed(() => {
    const now = new Date();
    
    return [
      {
        name: 'inventory_stock_items',
        records: store.stockItems.length,
        lastUpdated: getLastUpdated(store.stockItems, 'stock items')
      },
      {
        name: 'inventory_warehouses',
        records: store.warehouses.length,
        lastUpdated: getLastUpdated(store.warehouses, 'warehouses')
      },
      {
        name: 'inventory_suppliers',
        records: store.suppliers.length,
        lastUpdated: getLastUpdated(store.suppliers, 'suppliers')
      },
      {
        name: 'inventory_purchase_orders',
        records: store.purchaseOrders.length,
        lastUpdated: getLastUpdated(store.purchaseOrders, 'purchase orders')
      },
      {
        name: 'inventory_stock_movements',
        records: store.stockMovements.length,
        lastUpdated: getLastUpdated(store.stockMovements, 'stock movements')
      },
      {
        name: 'inventory_categories',
        records: new Set(store.stockItems.map(item => item.category)).size,
        lastUpdated: getLastUpdated(store.stockItems, 'categories')
      },
      {
        name: 'inventory_low_stock_alerts',
        records: store.getLowStockItems.length,
        lastUpdated: getLastUpdated(store.getLowStockItems, 'low stock alerts')
      },
      {
        name: 'inventory_reports',
        records: 0, // Reports are generated dynamically
        lastUpdated: 'never'
      },
      {
        name: 'inventory_audit_logs',
        records: 0, // Audit logs would be implemented
        lastUpdated: 'never'
      }
    ];
  });

  // Total records count for module description
  const totalRecords = computed(() => {
    return store.stockItems.length +
           store.warehouses.length +
           store.suppliers.length +
           store.purchaseOrders.length +
           store.stockMovements.length +
           new Set(store.stockItems.map(item => item.category)).size +
           store.getLowStockItems.length;
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
        const timestamp = item.timestamp || item.created_at || item.updated_at || item.date;
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
    title: `ERP Inventory Module v${versionsData.current} (${totalRecords.value} records)`,
    version: `v${versionsData.current}`,
    description: 'Comprehensive inventory management system with stock tracking, warehousing, and supply chain management.',
    totalRecords: totalRecords.value,
    descriptions: [
      {
        id: 1,
        title: 'Core Components',
        count: 9,
        list: [
          'Stock Management - Real-time stock tracking and monitoring',
          'Warehouse Management - Multi-warehouse capacity and zone management',
          'Supplier Management - Vendor relationship and performance tracking',
          'Purchase Orders - Complete PO lifecycle management',
          'Inventory Reporting - Advanced analytics and reporting',
          'Stock Movements - Transaction tracking and history',
          'Categories - Product categorization and organization',
          'Low Stock Alerts - Automated inventory alerts',
          'AI Integration - AI-powered optimization and insights'
        ]
      },
      {
        id: 2,
        title: 'UI Components',
        count: 7,
        list: [
          'InventoryButton - Auto-scaling button component with variants',
          'InventoryDropdown - Perfect height-matching dropdown component',
          'InventoryInput - Text input component matching dropdown dimensions exactly',
          'InventoryValueCard - Flexible value display cards with auto-coloring',
          'StatusBadge - Centralized status indicator component',
          'ScaledIcon - Dynamic icon sizing system',
          'InventoryLayout - Responsive layout components'
        ]
      },
      {
        id: 3,
        title: 'Features',
        count: 12,
        list: [
          'Complete Font Scaling System - All text elements scale dynamically with framework settings',
          'Real-time Stock Tracking - Live inventory updates and monitoring',
          'Multi-warehouse Support - Manage multiple warehouse locations',
          'Supplier Performance Tracking - Monitor vendor relationships and performance',
          'Purchase Order Management - Complete PO workflow from creation to fulfillment',
          'Inventory Analytics - Comprehensive reporting and data insights',
          'AI Agent Portal - AI-powered optimization and recommendations',
          'Dynamic UI Scaling - All components respond to font size changes',
          'Professional Dark Mode - Complete dark mode support across all components',
          'Responsive Design - Mobile-first approach with adaptive layouts',
          'Workflow Integration - Seamless integration with workflow management',
          'Audit Trail - Complete tracking of all inventory changes and movements'
        ]
      }
    ],
    features: [
      'Complete font scaling implementation across entire package',
      'Real-time stock level monitoring and alerts',
      'Multi-warehouse management with capacity tracking',
      'Supplier relationship management and performance tracking',
      'Purchase order creation and approval workflows',
      'Comprehensive inventory reporting and analytics',
      'AI-powered optimization and insights',
      'Dynamic font sizing for all text elements',
      'Professional dark mode support',
      'Responsive design with mobile-first approach'
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
      
      // Inventory package tables - use the actual tab IDs from Inventory.vue
      'inventory_stock_items': '#stock-management',
      'inventory_warehouses': '#warehouse-management',
      'inventory_suppliers': '#supplier-management',
      'inventory_purchase_orders': '#purchase-orders',
      'inventory_stock_movements': '#stock-management',
      'inventory_categories': '#stock-management',
      'inventory_low_stock_alerts': '#dashboard',
      'inventory_reports': '#reporting',
      'inventory_audit_logs': '#reporting'
    };
    
    const route = routes[entityType];
    if (route) {
      if (route.startsWith('#')) {
        // Handle hash-based navigation for inventory tabs
        const tabId = route.substring(1);
        // Emit a custom event that the Inventory.vue component can listen to
        window.dispatchEvent(new CustomEvent('switchInventoryTab', { 
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





