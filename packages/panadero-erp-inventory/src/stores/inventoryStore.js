import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import Decimal from 'decimal.js';
import axios from 'axios';

export const useInventoryStore = defineStore('inventory', () => {
  // Settings - centralized configuration
  const settings = ref({
    // Font scaling
    fontSize: 14,
    
    // Display
    darkMode: true,
    compactLayout: false,
    showDebug: false,
    
    // Tables
    rowsPerPage: 25,
    showRowNumbers: true,
    stickyHeaders: true,
    
    // Export
    defaultExportFormat: 'csv',
    includeHeaders: true,
    autoExport: false,
    
    // Data
    autoRefreshInterval: 30,
    lazyLoading: true,
    cacheData: true
  });

  // Settings actions
  const updateSetting = (key, value) => {
    settings.value[key] = value;
    localStorage.setItem('inventorySettings', JSON.stringify(settings.value));
  };

  const updateSettings = (newSettings) => {
    Object.assign(settings.value, newSettings);
    localStorage.setItem('inventorySettings', JSON.stringify(settings.value));
  };

  const resetSettings = () => {
    settings.value = {
      fontSize: 14,
      darkMode: true,
      compactLayout: false,
      showDebug: false,
      rowsPerPage: 25,
      showRowNumbers: true,
      stickyHeaders: true,
      defaultExportFormat: 'csv',
      includeHeaders: true,
      autoExport: false,
      autoRefreshInterval: 30,
      lazyLoading: true,
      cacheData: true
    };
    localStorage.setItem('inventorySettings', JSON.stringify(settings.value));
  };

  const loadSettings = () => {
    const saved = localStorage.getItem('inventorySettings');
    if (saved) {
      try {
        const parsed = JSON.parse(saved);
        Object.assign(settings.value, parsed);
      } catch (err) {
        console.error('Failed to load saved settings:', err);
      }
    }
  };

  // Font size specific actions
  const setFontSize = (size) => {
    settings.value.fontSize = size;
    localStorage.setItem('inventorySettings', JSON.stringify(settings.value));
  };

  // Simple form styles
  const forms = {
    input: 'w-full border rounded p-2',
    label: 'block text-sm font-medium mb-2',
    select: 'w-full border rounded p-2',
    textarea: 'w-full border rounded p-2 h-20'
  };

  // Core Inventory state (demo data)
  const stockItems = ref([
    {
      id: 1,
      sku: 'SKU-001',
      name: 'Widget A',
      category: 'Electronics',
      description: 'High-quality electronic widget',
      currentStock: 150,
      minStock: 50,
      maxStock: 500,
      unitCost: 25.99,
      unitPrice: 39.99,
      location: 'Warehouse A - Shelf 1',
      status: 'active',
      lastUpdated: '2025-08-31T10:00:00.000Z'
    },
    {
      id: 2,
      sku: 'SKU-002',
      name: 'Component B',
      category: 'Hardware',
      description: 'Essential hardware component',
      currentStock: 75,
      minStock: 25,
      maxStock: 200,
      unitCost: 15.50,
      unitPrice: 24.99,
      location: 'Warehouse B - Shelf 3',
      status: 'active',
      lastUpdated: '2025-08-31T09:30:00.000Z'
    }
  ]);

  const warehouses = ref([
    {
      id: 1,
      name: 'Main Warehouse',
      location: '123 Industrial Blvd',
      capacity: 10000,
      currentCapacity: 7500,
      manager: 'John Smith',
      status: 'active',
      zones: ['A', 'B', 'C']
    },
    {
      id: 2,
      name: 'Secondary Warehouse',
      location: '456 Storage St',
      capacity: 5000,
      currentCapacity: 3200,
      manager: 'Jane Doe',
      status: 'active',
      zones: ['D', 'E']
    }
  ]);

  const purchaseOrders = ref([
    {
      id: 1,
      poNumber: 'PO-2025-001',
      supplierId: 1,
      supplierName: 'ABC Supplies',
      orderDate: '2025-08-30',
      expectedDelivery: '2025-09-05',
      status: 'pending',
      totalAmount: 2500.00,
      items: [
        { sku: 'SKU-001', quantity: 100, unitCost: 25.99, totalCost: 2599.00 },
        { sku: 'SKU-002', quantity: 50, unitCost: 15.50, totalCost: 775.00 }
      ]
    }
  ]);

  const suppliers = ref([
    {
      id: 1,
      name: 'ABC Supplies',
      contactPerson: 'Mike Johnson',
      email: 'mike@abcsupplies.com',
      phone: '+1-555-0123',
      address: '789 Supply Ave',
      paymentTerms: 'Net 30',
      status: 'active',
      rating: 4.5
    },
    {
      id: 2,
      name: 'XYZ Components',
      contactPerson: 'Sarah Wilson',
      email: 'sarah@xyzcomponents.com',
      phone: '+1-555-0456',
      address: '321 Component St',
      paymentTerms: 'Net 15',
      status: 'active',
      rating: 4.2
    }
  ]);

  const stockMovements = ref([
    {
      id: 1,
      itemId: 1,
      sku: 'SKU-001',
      movementType: 'in',
      quantity: 100,
      reason: 'Purchase Order',
      reference: 'PO-2025-001',
      timestamp: '2025-08-30T14:30:00.000Z',
      user: 'John Smith'
    },
    {
      id: 2,
      itemId: 1,
      sku: 'SKU-001',
      movementType: 'out',
      quantity: 25,
      reason: 'Sales Order',
      reference: 'SO-2025-001',
      timestamp: '2025-08-31T09:15:00.000Z',
      user: 'Jane Doe'
    }
  ]);

  const inventoryReports = ref([
    {
      id: 1,
      reportType: 'stock_levels',
      title: 'Current Stock Levels',
      generatedAt: '2025-08-31T10:00:00.000Z',
      data: { totalItems: 225, lowStockItems: 3, outOfStockItems: 0 }
    },
    {
      id: 2,
      reportType: 'movement_summary',
      title: 'Stock Movement Summary',
      generatedAt: '2025-08-31T10:00:00.000Z',
      data: { totalMovements: 15, incoming: 8, outgoing: 7 }
    }
  ]);

  // Agent Portal data
  const agentPrompts = ref([
    {
      id: 1,
      title: 'Stock Level Analysis',
      prompt: 'Analyze current stock levels and identify items that need reordering',
      category: 'analysis',
      active: true
    },
    {
      id: 2,
      title: 'Supplier Performance',
      prompt: 'Review supplier performance metrics and suggest improvements',
      category: 'optimization',
      active: true
    },
    {
      id: 3,
      title: 'Inventory Optimization',
      prompt: 'Suggest inventory optimization strategies based on current data',
      category: 'optimization',
      active: true
    }
  ]);

  // Getters
  const getStockItems = computed(() => (filters = {}) => {
    let filtered = stockItems.value;
    if (filters.category) filtered = filtered.filter(item => item.category === filters.category);
    if (filters.status) filtered = filtered.filter(item => item.status === filters.status);
    if (filters.lowStock) filtered = filtered.filter(item => item.currentStock <= item.minStock);
    return filtered;
  });

  const getLowStockItems = computed(() => 
    stockItems.value.filter(item => item.currentStock <= item.minStock)
  );

  const getTotalInventoryValue = computed(() => 
    stockItems.value.reduce((total, item) => 
      total + (item.currentStock * item.unitCost), 0
    )
  );

  // Stock Management actions
  async function addStockItem(item) {
    const newItem = { ...item, id: Date.now(), status: 'active', lastUpdated: new Date().toISOString() };
    stockItems.value.push(newItem);
    return newItem;
  }

  async function updateStockLevel(itemId, newQuantity, reason, reference) {
    const item = stockItems.value.find(i => i.id === itemId);
    if (item) {
      const oldQuantity = item.currentStock;
      item.currentStock = newQuantity;
      item.lastUpdated = new Date().toISOString();
      
      // Record movement
      const movement = {
        id: Date.now(),
        itemId: itemId,
        sku: item.sku,
        movementType: newQuantity > oldQuantity ? 'in' : 'out',
        quantity: Math.abs(newQuantity - oldQuantity),
        reason: reason,
        reference: reference,
        timestamp: new Date().toISOString(),
        user: 'Current User'
      };
      stockMovements.value.push(movement);
    }
  }

  async function createPurchaseOrder(po) {
    const newPO = { ...po, id: Date.now(), status: 'draft', orderDate: new Date().toISOString() };
    purchaseOrders.value.push(newPO);
    return newPO;
  }

  async function addSupplier(supplier) {
    const newSupplier = { ...supplier, id: Date.now(), status: 'active' };
    suppliers.value.push(newSupplier);
    return newSupplier;
  }

  async function addWarehouse(warehouse) {
    const newWarehouse = { ...warehouse, id: Date.now(), status: 'active', currentCapacity: 0 };
    warehouses.value.push(newWarehouse);
    return newWarehouse;
  }

  async function generateReport(reportType, filters = {}) {
    const report = {
      id: Date.now(),
      reportType: reportType,
      title: `Generated ${reportType} Report`,
      generatedAt: new Date().toISOString(),
      data: { /* Report data based on type */ }
    };
    inventoryReports.value.push(report);
    return report;
  }

  // Agent Portal actions
  async function addAgentPrompt(prompt) {
    const newPrompt = { ...prompt, id: Date.now(), active: true };
    agentPrompts.value.push(newPrompt);
    return newPrompt;
  }

  async function executeAgentPrompt(promptId, context = {}) {
    const prompt = agentPrompts.value.find(p => p.id === promptId);
    if (prompt) {
      // Simulate AI processing
      console.log('Executing agent prompt:', prompt.title);
      console.log('Context:', context);
      return { success: true, result: 'AI processing completed' };
    }
    return { success: false, error: 'Prompt not found' };
  }

  return {
    // Settings
    settings,
    updateSetting,
    updateSettings,
    resetSettings,
    loadSettings,
    setFontSize,
    
    // Core data
    stockItems,
    warehouses,
    purchaseOrders,
    suppliers,
    stockMovements,
    inventoryReports,
    agentPrompts,
    
    // Getters
    getStockItems,
    getLowStockItems,
    getTotalInventoryValue,
    
    // Actions
    addStockItem,
    updateStockLevel,
    createPurchaseOrder,
    addSupplier,
    addWarehouse,
    generateReport,
    addAgentPrompt,
    executeAgentPrompt
  };
});
