/**
 * Seed Data for Panadero ERP Inventory Module
 * Centralized data source for all inventory-related sample data
 */

export const productGroups = [
  { id: 1, name: 'IT Equipment', description: 'Computers, monitors, accessories', color: 'blue' },
  { id: 2, name: 'Raw Materials', description: 'Basic materials for production', color: 'green' },
  { id: 3, name: 'Automotive', description: 'Vehicle parts and fluids', color: 'orange' },
  { id: 4, name: 'Construction', description: 'Building materials and tools', color: 'gray' },
  { id: 5, name: 'Office Supplies', description: 'Stationery and office equipment', color: 'purple' },
  { id: 6, name: 'Food & Beverage', description: 'Consumable food and drink items', color: 'yellow' }
]

export const productCategories = [
  { id: 1, name: 'Electronics', groupId: 1, description: 'Electronic devices and components' },
  { id: 2, name: 'Hardware', groupId: 1, description: 'Physical hardware components' },
  { id: 3, name: 'Grains', groupId: 2, description: 'Cereal grains and seeds' },
  { id: 4, name: 'Oils', groupId: 3, description: 'Lubricants and motor oils' },
  { id: 5, name: 'Tools', groupId: 4, description: 'Hand and power tools' },
  { id: 6, name: 'Stationery', groupId: 5, description: 'Writing and office supplies' },
  { id: 7, name: 'Beverages', groupId: 6, description: 'Drinks and liquid consumables' }
]

export const products = [
  {
    id: 1,
    sku: 'LAPTOP-DELL-001',
    name: 'Dell Laptop XPS 13',
    description: 'High-performance business laptop with 16GB RAM and 512GB SSD',
    categoryId: 1,
    groupId: 1,
    type: 'items',
    unit: 'units',
    currentStock: 15,
    minStock: 5,
    maxStock: 50,
    unitCost: 1299.99,
    unitPrice: 1599.99,
    status: 'active',
    supplierId: 1,
    warehouseId: 1,
    zone: 'A1',
    shelf: 'A1-S1',
    bin: 'A1-S1-B1',
    typeSpecificProperties: {
      weight: '1.27kg',
      dimensions: '295.7 x 198.7 x 14.8 mm',
      processor: 'Intel Core i7-1165G7',
      memory: '16GB LPDDR4x',
      storage: '512GB PCIe SSD',
      display: '13.4" FHD+ InfinityEdge',
      operatingSystem: 'Windows 11 Pro',
      warranty: '3 years'
    },
    metadata: {
      reorderPoint: 5,
      reservedStock: 2,
      lastRestocked: '2025-01-10',
      lastSold: '2025-01-15'
    }
  },
  {
    id: 2,
    sku: 'MONITOR-SAMSUNG-001',
    name: 'Samsung 27" 4K Monitor',
    description: 'Professional 4K UHD monitor with HDR support',
    categoryId: 1,
    groupId: 1,
    type: 'items',
    unit: 'units',
    currentStock: 8,
    minStock: 3,
    maxStock: 25,
    unitCost: 399.99,
    unitPrice: 549.99,
    status: 'active',
    supplierId: 2,
    warehouseId: 1,
    zone: 'A2',
    shelf: 'A2-S1',
    bin: 'A2-S1-B1',
    typeSpecificProperties: {
      weight: '5.2kg',
      dimensions: '614.4 x 366.2 x 55.0 mm',
      resolution: '3840 x 2160',
      refreshRate: '60Hz',
      responseTime: '4ms',
      connectivity: 'HDMI, DisplayPort, USB-C',
      colorGamut: '99% sRGB',
      brightness: '300 cd/m²'
    },
    metadata: {
      reorderPoint: 3,
      reservedStock: 1,
      lastRestocked: '2025-01-08',
      lastSold: '2025-01-14'
    }
  },
  {
    id: 3,
    sku: 'STEEL-BEAM-001',
    name: 'Steel I-Beam 6m',
    description: 'Structural steel I-beam for construction',
    categoryId: 5,
    groupId: 4,
    type: 'bulk',
    unit: 'meters',
    currentStock: 120.5,
    minStock: 50,
    maxStock: 500,
    unitCost: 45.00,
    unitPrice: 65.00,
    status: 'active',
    supplierId: 3,
    warehouseId: 2,
    zone: 'B1',
    shelf: 'B1-S1',
    bin: 'B1-S1-B1',
    typeSpecificProperties: {
      material: 'Carbon Steel',
      grade: 'S275',
      length: '6 meters',
      weight: '12.5 kg/m',
      crossSection: '200x100x5.5mm',
      surface: 'Hot-dip galvanized',
      standards: 'EN 10025-2'
    },
    metadata: {
      reorderPoint: 50,
      reservedStock: 0,
      lastRestocked: '2025-01-12',
      lastSold: '2025-01-16'
    }
  },
  {
    id: 4,
    sku: 'MOTOR-OIL-001',
    name: 'Synthetic Motor Oil 5W-30',
    description: 'High-performance synthetic motor oil',
    categoryId: 4,
    groupId: 3,
    type: 'liquids',
    unit: 'liters',
    currentStock: 45.0,
    minStock: 20,
    maxStock: 200,
    unitCost: 12.50,
    unitPrice: 18.99,
    status: 'active',
    supplierId: 4,
    warehouseId: 1,
    zone: 'C1',
    shelf: 'C1-S1',
    bin: 'C1-S1-B1',
    typeSpecificProperties: {
      viscosity: '5W-30',
      type: 'Synthetic',
      volume: '4 liters',
      container: 'Plastic bottle',
      specifications: 'API SN/CF, ACEA A3/B4',
      temperature: '-30°C to +40°C',
      changeInterval: '15,000 km'
    },
    metadata: {
      reorderPoint: 20,
      reservedStock: 5,
      lastRestocked: '2025-01-09',
      lastSold: '2025-01-13'
    }
  },
  {
    id: 5,
    sku: 'PAPER-A4-001',
    name: 'A4 Copy Paper 80gsm',
    description: 'High-quality white copy paper',
    categoryId: 6,
    groupId: 5,
    type: 'items',
    unit: 'reams',
    currentStock: 25,
    minStock: 10,
    maxStock: 100,
    unitCost: 4.50,
    unitPrice: 6.99,
    status: 'active',
    supplierId: 5,
    warehouseId: 1,
    zone: 'D1',
    shelf: 'D1-S1',
    bin: 'D1-S1-B1',
    typeSpecificProperties: {
      size: 'A4 (210 x 297 mm)',
      weight: '80 gsm',
      color: 'White',
      brightness: '96%',
      opacity: '94%',
      sheets: '500 sheets per ream',
      packaging: '5 reams per box',
      standards: 'ISO 216'
    },
    metadata: {
      reorderPoint: 10,
      reservedStock: 0,
      lastRestocked: '2025-01-11',
      lastSold: '2025-01-15'
    }
  }
]

export const vendors = [
  {
    id: 1,
    name: 'Dell Technologies',
    type: 'vendor',
    contactPerson: 'John Smith',
    email: 'john.smith@dell.com',
    phone: '+1-555-0123',
    address: 'One Dell Way, Round Rock, TX 78682',
    rating: 4.8,
    paymentTerms: 'Net 30',
    status: 'active',
    totalOrders: 45,
    totalValue: 125000.00,
    lastOrderDate: '2025-01-10',
    notes: 'Reliable supplier for IT equipment'
  },
  {
    id: 2,
    name: 'Samsung Electronics',
    type: 'vendor',
    contactPerson: 'Sarah Johnson',
    email: 'sarah.johnson@samsung.com',
    phone: '+1-555-0124',
    address: '85 Challenger Rd, Ridgefield Park, NJ 07660',
    rating: 4.6,
    paymentTerms: 'Net 45',
    status: 'active',
    totalOrders: 32,
    totalValue: 89000.00,
    lastOrderDate: '2025-01-08',
    notes: 'Premium display and electronics supplier'
  },
  {
    id: 3,
    name: 'SteelCorp Industries',
    type: 'vendor',
    contactPerson: 'Mike Wilson',
    email: 'mike.wilson@steelcorp.com',
    phone: '+1-555-0125',
    address: '123 Industrial Blvd, Pittsburgh, PA 15222',
    rating: 4.4,
    paymentTerms: 'Net 15',
    status: 'active',
    totalOrders: 28,
    totalValue: 156000.00,
    lastOrderDate: '2025-01-12',
    notes: 'Construction materials and structural steel'
  },
  {
    id: 4,
    name: 'LubriMax Solutions',
    type: 'vendor',
    contactPerson: 'Lisa Brown',
    email: 'lisa.brown@lubrimax.com',
    phone: '+1-555-0126',
    address: '456 Oil Street, Houston, TX 77001',
    rating: 4.2,
    paymentTerms: 'Net 30',
    status: 'active',
    totalOrders: 15,
    totalValue: 23000.00,
    lastOrderDate: '2025-01-09',
    notes: 'Automotive fluids and lubricants'
  },
  {
    id: 5,
    name: 'OfficeMax Supplies',
    type: 'vendor',
    contactPerson: 'David Lee',
    email: 'david.lee@officemax.com',
    phone: '+1-555-0127',
    address: '789 Business Park, Chicago, IL 60601',
    rating: 4.0,
    paymentTerms: 'Net 30',
    status: 'active',
    totalOrders: 22,
    totalValue: 8500.00,
    lastOrderDate: '2025-01-11',
    notes: 'Office supplies and stationery'
  }
]

export const customers = [
  {
    id: 1,
    name: 'ABC Corporation',
    type: 'customer',
    contactPerson: 'Mike Wilson',
    email: 'mike.wilson@abccorp.com',
    phone: '+1-555-0201',
    address: '100 Corporate Plaza, New York, NY 10001',
    rating: 4.9,
    creditLimit: 100000.00,
    status: 'active',
    totalOrders: 67,
    totalValue: 245000.00,
    lastOrderDate: '2025-01-15',
    notes: 'Premium customer with excellent payment history'
  },
  {
    id: 2,
    name: 'TechStart Inc',
    type: 'customer',
    contactPerson: 'Jennifer Davis',
    email: 'jennifer.davis@techstart.com',
    phone: '+1-555-0202',
    address: '200 Innovation Drive, San Francisco, CA 94105',
    rating: 4.7,
    creditLimit: 50000.00,
    status: 'active',
    totalOrders: 23,
    totalValue: 89000.00,
    lastOrderDate: '2025-01-14',
    notes: 'Growing tech company, regular orders'
  },
  {
    id: 3,
    name: 'BuildRight Construction',
    type: 'customer',
    contactPerson: 'Robert Garcia',
    email: 'robert.garcia@buildright.com',
    phone: '+1-555-0203',
    address: '300 Construction Way, Dallas, TX 75201',
    rating: 4.5,
    creditLimit: 75000.00,
    status: 'active',
    totalOrders: 34,
    totalValue: 178000.00,
    lastOrderDate: '2025-01-13',
    notes: 'Major construction company, bulk orders'
  }
]

export const forwarders = [
  {
    id: 1,
    name: 'FastLogistics Inc',
    type: 'forwarder',
    contactPerson: 'Lisa Brown',
    email: 'lisa.brown@fastlogistics.com',
    phone: '+1-555-0301',
    address: '400 Logistics Park, Memphis, TN 38118',
    rating: 4.6,
    services: ['Air Freight', 'Sea Freight', 'Ground Transport'],
    status: 'active',
    totalShipments: 156,
    onTimeDelivery: 94.5,
    lastShipment: '2025-01-14',
    notes: 'Reliable logistics partner with global network'
  },
  {
    id: 2,
    name: 'Global Shipping Co',
    type: 'forwarder',
    contactPerson: 'Mark Thompson',
    email: 'mark.thompson@globalshipping.com',
    phone: '+1-555-0302',
    address: '500 Port Avenue, Los Angeles, CA 90731',
    rating: 4.4,
    services: ['Sea Freight', 'Air Freight', 'Customs Clearance'],
    status: 'active',
    totalShipments: 89,
    onTimeDelivery: 91.2,
    lastShipment: '2025-01-12',
    notes: 'International shipping specialist'
  }
]

export const stockLevels = [
  {
    id: 1,
    productId: 1,
    sku: 'LAPTOP-DELL-001',
    name: 'Dell Laptop XPS 13',
    currentStock: 15,
    minStock: 5,
    maxStock: 50,
    reservedStock: 2,
    availableStock: 13,
    unit: 'units',
    unitCost: 1299.99,
    totalValue: 19499.85,
    lastUpdated: '2025-01-15T10:30:00Z',
    status: 'in_stock',
    warehouseId: 1,
    zone: 'A1',
    reorderPoint: 5,
    daysToReorder: 12
  },
  {
    id: 2,
    productId: 2,
    sku: 'MONITOR-SAMSUNG-001',
    name: 'Samsung 27" 4K Monitor',
    currentStock: 8,
    minStock: 3,
    maxStock: 25,
    reservedStock: 1,
    availableStock: 7,
    unit: 'units',
    unitCost: 399.99,
    totalValue: 3199.92,
    lastUpdated: '2025-01-14T14:20:00Z',
    status: 'in_stock',
    warehouseId: 1,
    zone: 'A2',
    reorderPoint: 3,
    daysToReorder: 8
  },
  {
    id: 3,
    productId: 3,
    sku: 'STEEL-BEAM-001',
    name: 'Steel I-Beam 6m',
    currentStock: 120.5,
    minStock: 50,
    maxStock: 500,
    reservedStock: 0,
    availableStock: 120.5,
    unit: 'meters',
    unitCost: 45.00,
    totalValue: 5422.50,
    lastUpdated: '2025-01-16T09:15:00Z',
    status: 'in_stock',
    warehouseId: 2,
    zone: 'B1',
    reorderPoint: 50,
    daysToReorder: 25
  },
  {
    id: 4,
    productId: 4,
    sku: 'MOTOR-OIL-001',
    name: 'Synthetic Motor Oil 5W-30',
    currentStock: 45.0,
    minStock: 20,
    maxStock: 200,
    reservedStock: 5,
    availableStock: 40.0,
    unit: 'liters',
    unitCost: 12.50,
    totalValue: 562.50,
    lastUpdated: '2025-01-13T16:45:00Z',
    status: 'in_stock',
    warehouseId: 1,
    zone: 'C1',
    reorderPoint: 20,
    daysToReorder: 15
  },
  {
    id: 5,
    productId: 5,
    sku: 'PAPER-A4-001',
    name: 'A4 Copy Paper 80gsm',
    currentStock: 25,
    minStock: 10,
    maxStock: 100,
    reservedStock: 0,
    availableStock: 25,
    unit: 'reams',
    unitCost: 4.50,
    totalValue: 112.50,
    lastUpdated: '2025-01-15T11:30:00Z',
    status: 'in_stock',
    warehouseId: 1,
    zone: 'D1',
    reorderPoint: 10,
    daysToReorder: 20
  }
]

export const incomingOrders = [
  {
    id: 1,
    orderNumber: 'PO-2025-001',
    supplier: 'Dell Technologies',
    orderDate: '2025-01-10',
    expectedDate: '2025-01-20',
    status: 'pending',
    totalValue: 25999.80,
    items: [
      { sku: 'LAPTOP-DELL-001', name: 'Dell Laptop XPS 13', quantity: 20, unitCost: 1299.99 }
    ],
    notes: 'Urgent order for Q1 inventory replenishment'
  },
  {
    id: 2,
    orderNumber: 'PO-2025-002',
    supplier: 'Samsung Electronics',
    orderDate: '2025-01-08',
    expectedDate: '2025-01-18',
    status: 'in_transit',
    totalValue: 15999.84,
    items: [
      { sku: 'MONITOR-SAMSUNG-001', name: 'Samsung 27" 4K Monitor', quantity: 32, unitCost: 399.99 }
    ],
    notes: 'Monitor stock replenishment'
  },
  {
    id: 3,
    orderNumber: 'PO-2025-003',
    supplier: 'SteelCorp Industries',
    orderDate: '2025-01-12',
    expectedDate: '2025-01-25',
    status: 'pending',
    totalValue: 13500.00,
    items: [
      { sku: 'STEEL-BEAM-001', name: 'Steel I-Beam 6m', quantity: 300, unitCost: 45.00 }
    ],
    notes: 'Construction project materials'
  }
]

export const outgoingOrders = [
  {
    id: 1,
    orderNumber: 'SO-2025-001',
    customer: 'ABC Corporation',
    orderDate: '2025-01-12',
    expectedDate: '2025-01-19',
    status: 'processing',
    totalValue: 15999.99,
    items: [
      { sku: 'LAPTOP-DELL-001', name: 'Dell Laptop XPS 13', quantity: 10, unitPrice: 1599.99 }
    ],
    notes: 'Corporate laptop upgrade program'
  },
  {
    id: 2,
    orderNumber: 'SO-2025-002',
    customer: 'TechStart Inc',
    orderDate: '2025-01-14',
    expectedDate: '2025-01-21',
    status: 'pending',
    totalValue: 2199.96,
    items: [
      { sku: 'MONITOR-SAMSUNG-001', name: 'Samsung 27" 4K Monitor', quantity: 4, unitPrice: 549.99 }
    ],
    notes: 'Office setup for new employees'
  },
  {
    id: 3,
    orderNumber: 'SO-2025-003',
    customer: 'BuildRight Construction',
    orderDate: '2025-01-13',
    expectedDate: '2025-01-22',
    status: 'processing',
    totalValue: 19500.00,
    items: [
      { sku: 'STEEL-BEAM-001', name: 'Steel I-Beam 6m', quantity: 300, unitPrice: 65.00 }
    ],
    notes: 'Construction project materials'
  }
]

export const closeoutPeriods = [
  {
    id: 1,
    period: '2025-01',
    type: 'monthly',
    startDate: '2025-01-01',
    endDate: '2025-01-31',
    status: 'completed',
    totalValue: 125000.00,
    items: [
      { sku: 'LAPTOP-DELL-001', startStock: 20, incoming: 25, outgoing: 30, adjustments: 0, endStock: 15, value: 19499.85 },
      { sku: 'MONITOR-SAMSUNG-001', startStock: 12, incoming: 20, outgoing: 24, adjustments: 0, endStock: 8, value: 3199.92 },
      { sku: 'STEEL-BEAM-001', startStock: 100, incoming: 200, outgoing: 179.5, adjustments: 0, endStock: 120.5, value: 5422.50 }
    ],
    notes: 'January 2025 monthly closeout completed'
  },
  {
    id: 2,
    period: '2024-12',
    type: 'monthly',
    startDate: '2024-12-01',
    endDate: '2024-12-31',
    status: 'completed',
    totalValue: 118000.00,
    items: [
      { sku: 'LAPTOP-DELL-001', startStock: 15, incoming: 30, outgoing: 25, adjustments: 0, endStock: 20, value: 25999.80 },
      { sku: 'MONITOR-SAMSUNG-001', startStock: 8, incoming: 15, outgoing: 11, adjustments: 0, endStock: 12, value: 4799.88 }
    ],
    notes: 'December 2024 monthly closeout completed'
  }
]

export const filterOptions = [
  { value: 'today', label: 'Today' },
  { value: 'thisWeek', label: 'This Week' },
  { value: 'thisMonth', label: 'This Month' },
  { value: 'custom', label: 'Custom Range' }
]

export const statusOptions = [
  { value: 'all', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'pending', label: 'Pending' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' }
]

export const typeOptions = [
  { value: 'all', label: 'All Types' },
  { value: 'items', label: 'Items' },
  { value: 'bulk', label: 'Bulk' },
  { value: 'liquids', label: 'Liquids' }
]

export const priorityOptions = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'urgent', label: 'Urgent' }
]

export const colorOptions = [
  { value: 'blue', label: 'Blue' },
  { value: 'green', label: 'Green' },
  { value: 'red', label: 'Red' },
  { value: 'yellow', label: 'Yellow' },
  { value: 'purple', label: 'Purple' },
  { value: 'orange', label: 'Orange' },
  { value: 'gray', label: 'Gray' }
]
