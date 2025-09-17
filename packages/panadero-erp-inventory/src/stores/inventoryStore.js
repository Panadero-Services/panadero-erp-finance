import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import Decimal from 'decimal.js';
import axios from 'axios';

export const useInventoryStore = defineStore('inventory', () => {
  // Stock type definitions
  const STOCK_TYPES = {
    ITEMS: 'items',      // Discrete units (laptops, monitors, cables)
    BULK: 'bulk',        // Granular materials (grains, powders, sand)
    LIQUIDS: 'liquids'   // Fluids (oils, chemicals, beverages)
  };

  // Settings - centralized configuration
  const settings = ref({
    // Font scaling
    fontSize: 14,
    
    // Display
    darkMode: false,
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
      darkMode: false,
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
    label: 'block font-medium mb-2',
    select: 'w-full border rounded p-2',
    textarea: 'w-full border rounded p-2 h-20'
  };

  // Core Inventory state with three stock types
  const stockItems = ref([
    // ITEMS - Discrete units
    {
      id: 1,
      sku: 'LAPTOP-DELL-001',
      name: 'Dell Latitude 5520 Laptop',
      stockType: STOCK_TYPES.ITEMS,
      category: 'IT Equipment',
      description: 'Business laptop - 15.6" FHD, i7, 16GB RAM, 512GB SSD',
      currentStock: 45,
      minStock: 20,
      maxStock: 100,
      unit: 'units',
      unitCost: 899.00,
      unitPrice: 1299.00,
      location: 'Main Warehouse - Zone A-12',
      status: 'active',
      lastUpdated: '2025-01-16T08:30:00.000Z',
      // Items-specific properties
      serialNumbers: ['SN001', 'SN002', 'SN003'],
      condition: 'new',
      warranty: '3 years',
      incoming: [
        { quantity: 25, expectedDate: '2025-01-17T14:00:00.000Z', supplier: 'Dell Direct', poNumber: 'PO-2025-001' },
        { quantity: 15, expectedDate: '2025-01-20T10:00:00.000Z', supplier: 'TechDistributors', poNumber: 'PO-2025-003' }
      ],
      outgoing: [
        { quantity: 8, scheduledDate: '2025-01-17T09:00:00.000Z', customer: 'ABC Corp', soNumber: 'SO-2025-156' },
        { quantity: 12, scheduledDate: '2025-01-18T11:00:00.000Z', customer: 'XYZ Ltd', soNumber: 'SO-2025-189' }
      ]
    },
    {
      id: 2,
      sku: 'MONITOR-SAMSUNG-001',
      name: 'Samsung 24" Business Monitor',
      stockType: STOCK_TYPES.ITEMS,
      category: 'IT Equipment',
      description: '24" FHD LED monitor, VGA/HDMI/DP, adjustable stand',
      currentStock: 78,
      minStock: 30,
      maxStock: 150,
      unit: 'units',
      unitCost: 189.00,
      unitPrice: 279.00,
      location: 'Main Warehouse - Zone A-15',
      status: 'active',
      lastUpdated: '2025-01-16T09:15:00.000Z',
      serialNumbers: [],
      condition: 'new',
      warranty: '2 years',
      incoming: [
        { quantity: 50, expectedDate: '2025-01-18T16:00:00.000Z', supplier: 'Samsung Direct', poNumber: 'PO-2025-002' }
      ],
      outgoing: [
        { quantity: 15, scheduledDate: '2025-01-17T10:30:00.000Z', customer: 'TechStart Inc', soNumber: 'SO-2025-167' },
        { quantity: 8, scheduledDate: '2025-01-19T14:00:00.000Z', customer: 'Office Solutions', soNumber: 'SO-2025-201' }
      ]
    },

    // BULK - Granular materials
    {
      id: 3,
      sku: 'GRAIN-WHEAT-001',
      name: 'Premium Wheat Grain',
      stockType: STOCK_TYPES.BULK,
      category: 'Raw Materials',
      description: 'High-quality wheat grain for food production',
      currentStock: 2500,
      minStock: 500,
      maxStock: 5000,
      unit: 'kg',
      unitCost: 0.45,
      unitPrice: 0.65,
      location: 'Bulk Storage - Silo A',
      status: 'active',
      lastUpdated: '2025-01-16T07:30:00.000Z',
      // Bulk-specific properties
      moistureContent: 12.5,
      grade: 'A',
      origin: 'Canada',
      harvestDate: '2024-08-15',
      batchNumber: 'WHT-2024-08-001',
      incoming: [
        { quantity: 1000, expectedDate: '2025-01-19T08:00:00.000Z', supplier: 'GrainCo', poNumber: 'PO-2025-005' }
      ],
      outgoing: [
        { quantity: 200, scheduledDate: '2025-01-17T06:00:00.000Z', customer: 'Bakery A', soNumber: 'SO-2025-170' }
      ]
    },
    {
      id: 4,
      sku: 'SAND-CONSTRUCTION-001',
      name: 'Construction Sand',
      stockType: STOCK_TYPES.BULK,
      category: 'Construction Materials',
      description: 'Fine construction sand for concrete mixing',
      currentStock: 15000,
      minStock: 2000,
      maxStock: 20000,
      unit: 'tons',
      unitCost: 25.00,
      unitPrice: 35.00,
      location: 'Bulk Storage - Yard B',
      status: 'active',
      lastUpdated: '2025-01-16T06:45:00.000Z',
      grade: 'Fine',
      source: 'Local Quarry',
      batchNumber: 'SND-2025-01-001',
      incoming: [
        { quantity: 5000, expectedDate: '2025-01-20T12:00:00.000Z', supplier: 'SandCo', poNumber: 'PO-2025-006' }
      ],
      outgoing: [
        { quantity: 1000, scheduledDate: '2025-01-18T09:00:00.000Z', customer: 'Construction Co', soNumber: 'SO-2025-175' }
      ]
    },

    // LIQUIDS - Fluids
    {
      id: 5,
      sku: 'OIL-MOTOR-001',
      name: 'Motor Oil 10W-30',
      stockType: STOCK_TYPES.LIQUIDS,
      category: 'Automotive Fluids',
      description: 'High-performance motor oil, 10W-30 grade',
      currentStock: 500,
      minStock: 100,
      maxStock: 1000,
      unit: 'liters',
      unitCost: 8.50,
      unitPrice: 12.99,
      location: 'Liquid Storage - Tank 1',
      status: 'active',
      lastUpdated: '2025-01-16T10:20:00.000Z',
      // Liquid-specific properties
      viscosity: '10W-30',
      temperature: 20,
      expiryDate: '2026-12-31',
      batchNumber: 'OIL-2025-01-001',
      containerType: 'bulk_tank',
      incoming: [
        { quantity: 200, expectedDate: '2025-01-18T14:00:00.000Z', supplier: 'OilCo', poNumber: 'PO-2025-007' }
      ],
      outgoing: [
        { quantity: 50, scheduledDate: '2025-01-17T11:00:00.000Z', customer: 'Auto Shop', soNumber: 'SO-2025-180' }
      ]
    },
    {
      id: 6,
      sku: 'CHEMICAL-CLEANER-001',
      name: 'Industrial Cleaner',
      stockType: STOCK_TYPES.LIQUIDS,
      category: 'Cleaning Chemicals',
      description: 'Heavy-duty industrial cleaning solution',
      currentStock: 200,
      minStock: 50,
      maxStock: 500,
      unit: 'liters',
      unitCost: 15.00,
      unitPrice: 22.50,
      location: 'Liquid Storage - Tank 2',
      status: 'active',
      lastUpdated: '2025-01-16T11:00:00.000Z',
      phLevel: 11.5,
      temperature: 18,
      expiryDate: '2025-06-30',
      batchNumber: 'CHM-2025-01-001',
      containerType: 'drum',
      incoming: [
        { quantity: 100, expectedDate: '2025-01-19T10:00:00.000Z', supplier: 'ChemCo', poNumber: 'PO-2025-008' }
      ],
      outgoing: [
        { quantity: 25, scheduledDate: '2025-01-17T13:00:00.000Z', customer: 'Factory A', soNumber: 'SO-2025-185' }
      ]
    }
  ]);

  // Keep existing warehouses, purchaseOrders, suppliers, etc. (unchanged)
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

  // Stock type specific getters
  const getStockByType = computed(() => (stockType) => {
    return stockItems.value.filter(item => item.stockType === stockType);
  });

  const getItemsStock = computed(() => getStockByType.value(STOCK_TYPES.ITEMS));
  const getBulkStock = computed(() => getStockByType.value(STOCK_TYPES.BULK));
  const getLiquidsStock = computed(() => getStockByType.value(STOCK_TYPES.LIQUIDS));

  // Stock type specific metrics
  const getStockTypeMetrics = computed(() => (stockType) => {
    const items = getStockByType.value(stockType);
    const totalCurrent = items.reduce((sum, item) => sum + item.currentStock, 0);
    const totalValue = items.reduce((sum, item) => sum + (item.currentStock * item.unitCost), 0);
    const lowStockItems = items.filter(item => item.currentStock <= item.minStock).length;
    
    return {
      totalItems: items.length,
      totalCurrent,
      totalValue,
      lowStockItems,
      averageStockLevel: totalCurrent / items.length || 0
    };
  });

  // Universal stock projection (works for all types)
  const getProjectedStock = computed(() => (sku, daysAhead = 1) => {
    const item = stockItems.value.find(i => i.sku === sku);
    if (!item) return 0;
    
    const current = item.currentStock;
    const targetDate = new Date(Date.now() + (daysAhead * 24 * 60 * 60 * 1000));
    
    const incoming = item.incoming
      ?.filter(inc => new Date(inc.expectedDate) <= targetDate)
      .reduce((total, inc) => total + inc.quantity, 0) || 0;
    
    const outgoing = item.outgoing
      ?.filter(out => new Date(out.scheduledDate) <= targetDate)
      .reduce((total, out) => total + out.quantity, 0) || 0;
    
    return current + incoming - outgoing;
  });

  // Multi-period logistics overview
  const getLogisticsOverview = computed(() => (timeHorizons = [1, 3, 7, 14]) => {
    const overview = {};
    
    timeHorizons.forEach(days => {
      const totalCurrent = stockItems.value.reduce((sum, item) => sum + item.currentStock, 0);
      const totalIncoming = stockItems.value.reduce((sum, item) => 
        sum + (item.incoming
          ?.filter(inc => new Date(inc.expectedDate) <= new Date(Date.now() + (days * 24 * 60 * 60 * 1000)))
          .reduce((incSum, inc) => incSum + inc.quantity, 0) || 0), 0);
      const totalOutgoing = stockItems.value.reduce((sum, item) => 
        sum + (item.outgoing
          ?.filter(out => new Date(out.scheduledDate) <= new Date(Date.now() + (days * 24 * 60 * 60 * 1000)))
          .reduce((outSum, out) => outSum + out.quantity, 0) || 0), 0);
      
      overview[`day${days}`] = {
        currentStock: totalCurrent,
        incomingStock: totalIncoming,
        outgoingStock: totalOutgoing,
        projectedStock: totalCurrent + totalIncoming - totalOutgoing,
        lowStockItems: stockItems.value.filter(item => 
          getProjectedStock.value(item.sku, days) <= item.minStock
        ).length,
        criticalItems: stockItems.value.filter(item => 
          getProjectedStock.value(item.sku, days) <= 0
        ).length
      };
    });
    
    return overview;
  });

  // Existing getters (updated to work with new structure)
  const getStockItems = computed(() => (filters = {}) => {
    let filtered = stockItems.value;
    if (filters.category) filtered = filtered.filter(item => item.category === filters.category);
    if (filters.status) filtered = filtered.filter(item => item.status === filters.status);
    if (filters.stockType) filtered = filtered.filter(item => item.stockType === filters.stockType);
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

  // Existing actions (unchanged)
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

  async function addAgentPrompt(prompt) {
    const newPrompt = { ...prompt, id: Date.now(), active: true };
    agentPrompts.value.push(newPrompt);
    return newPrompt;
  }

  async function executeAgentPrompt(promptId, context = {}) {
    const prompt = agentPrompts.value.find(p => p.id === promptId);
    if (prompt) {
      console.log('Executing agent prompt:', prompt.title);
      console.log('Context:', context);
      return { success: true, result: 'AI processing completed' };
    }
    return { success: false, error: 'Prompt not found' };
  }

  return {
    // Constants
    STOCK_TYPES,
    
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
    
    // Stock type getters
    getStockByType,
    getItemsStock,
    getBulkStock,
    getLiquidsStock,
    getStockTypeMetrics,
    
    // Logistics getters
    getProjectedStock,
    getLogisticsOverview,
    
    // Existing getters
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