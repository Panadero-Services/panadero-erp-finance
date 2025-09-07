<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryPurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchaseOrders = [
            // Recent Orders - Pending
            [
                'po_number' => 'PO-2025-001',
                'supplier_id' => 1,
                'supplier_name' => 'ABC Electronics Supply',
                'order_date' => '2025-08-30',
                'expected_delivery' => '2025-09-05',
                'status' => 'pending',
                'total_amount' => 2599.00,
                'notes' => 'Urgent order for Q4 production - Arduino boards',
                'items' => json_encode([
                    [
                        'sku' => 'ELC-001',
                        'quantity' => 100,
                        'unit_cost' => 18.50,
                        'total_cost' => 1850.00
                    ],
                    [
                        'sku' => 'ELC-002',
                        'quantity' => 20,
                        'unit_cost' => 45.00,
                        'total_cost' => 900.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'po_number' => 'PO-2025-002',
                'supplier_id' => 2,
                'supplier_name' => 'Hardware Solutions Ltd',
                'order_date' => '2025-08-29',
                'expected_delivery' => '2025-09-10',
                'status' => 'pending',
                'total_amount' => 3875.00,
                'notes' => 'Monthly hardware restock',
                'items' => json_encode([
                    [
                        'sku' => 'HWD-001',
                        'quantity' => 50,
                        'unit_cost' => 12.99,
                        'total_cost' => 649.50
                    ],
                    [
                        'sku' => 'HWD-002',
                        'quantity' => 100,
                        'unit_cost' => 8.50,
                        'total_cost' => 850.00
                    ],
                    [
                        'sku' => 'HWD-003',
                        'quantity' => 200,
                        'unit_cost' => 15.00,
                        'total_cost' => 3000.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],

            // Approved Orders
            [
                'po_number' => 'PO-2025-003',
                'supplier_id' => 3,
                'supplier_name' => 'Global Materials Co',
                'order_date' => '2025-08-28',
                'expected_delivery' => '2025-09-15',
                'status' => 'approved',
                'total_amount' => 15000.00,
                'notes' => 'Bulk order for new project - approved by manager',
                'items' => json_encode([
                    [
                        'sku' => 'RWM-001',
                        'quantity' => 100,
                        'unit_cost' => 12.99,
                        'total_cost' => 1299.00
                    ],
                    [
                        'sku' => 'RWM-002',
                        'quantity' => 500,
                        'unit_cost' => 2.50,
                        'total_cost' => 1250.00
                    ],
                    [
                        'sku' => 'RWM-003',
                        'quantity' => 200,
                        'unit_cost' => 8.99,
                        'total_cost' => 1798.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'po_number' => 'PO-2025-004',
                'supplier_id' => 4,
                'supplier_name' => 'Tech Tools & More',
                'order_date' => '2025-08-27',
                'expected_delivery' => '2025-09-01',
                'status' => 'approved',
                'total_amount' => 1399.50,
                'notes' => 'Tools for maintenance department',
                'items' => json_encode([
                    [
                        'sku' => 'TOL-001',
                        'quantity' => 5,
                        'unit_cost' => 45.00,
                        'total_cost' => 225.00
                    ],
                    [
                        'sku' => 'TOL-002',
                        'quantity' => 3,
                        'unit_cost' => 35.00,
                        'total_cost' => 105.00
                    ],
                    [
                        'sku' => 'TOL-003',
                        'quantity' => 10,
                        'unit_cost' => 18.50,
                        'total_cost' => 185.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subHours(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Completed Orders
            [
                'po_number' => 'PO-2025-005',
                'supplier_id' => 5,
                'supplier_name' => 'Accessory World Inc',
                'order_date' => '2025-08-25',
                'expected_delivery' => '2025-08-30',
                'status' => 'completed',
                'total_amount' => 899.50,
                'notes' => 'Accessories for customer orders - delivered on time',
                'items' => json_encode([
                    [
                        'sku' => 'ACC-001',
                        'quantity' => 100,
                        'unit_cost' => 3.99,
                        'total_cost' => 399.00
                    ],
                    [
                        'sku' => 'ACC-002',
                        'quantity' => 50,
                        'unit_cost' => 8.99,
                        'total_cost' => 449.50
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(3),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(1),
            ],
            [
                'po_number' => 'PO-2025-006',
                'supplier_id' => 1,
                'supplier_name' => 'ABC Electronics Supply',
                'order_date' => '2025-08-20',
                'expected_delivery' => '2025-08-25',
                'status' => 'completed',
                'total_amount' => 2250.00,
                'notes' => 'ESP32 boards for IoT project - completed successfully',
                'items' => json_encode([
                    [
                        'sku' => 'ELC-003',
                        'quantity' => 250,
                        'unit_cost' => 8.99,
                        'total_cost' => 2247.50
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(6),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(2),
            ],

            // Draft Orders
            [
                'po_number' => 'PO-2025-007',
                'supplier_id' => 2,
                'supplier_name' => 'Hardware Solutions Ltd',
                'order_date' => '2025-08-31',
                'expected_delivery' => '2025-09-20',
                'status' => 'draft',
                'total_amount' => 5500.00,
                'notes' => 'Draft order for aluminum profiles - pending review',
                'items' => json_encode([
                    [
                        'sku' => 'HWD-004',
                        'quantity' => 200,
                        'unit_cost' => 8.99,
                        'total_cost' => 1798.00
                    ],
                    [
                        'sku' => 'HWD-005',
                        'quantity' => 1000,
                        'unit_cost' => 0.45,
                        'total_cost' => 450.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'po_number' => 'PO-2025-008',
                'supplier_id' => 3,
                'supplier_name' => 'Global Materials Co',
                'order_date' => '2025-08-31',
                'expected_delivery' => '2025-09-25',
                'status' => 'draft',
                'total_amount' => 3200.00,
                'notes' => 'Draft order for raw materials - awaiting budget approval',
                'items' => json_encode([
                    [
                        'sku' => 'RWM-004',
                        'quantity' => 100,
                        'unit_cost' => 15.00,
                        'total_cost' => 1500.00
                    ],
                    [
                        'sku' => 'RWM-005',
                        'quantity' => 200,
                        'unit_cost' => 4.99,
                        'total_cost' => 998.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],

            // More Recent Orders
            [
                'po_number' => 'PO-2025-009',
                'supplier_id' => 4,
                'supplier_name' => 'Tech Tools & More',
                'order_date' => '2025-08-30',
                'expected_delivery' => '2025-09-08',
                'status' => 'pending',
                'total_amount' => 1899.00,
                'notes' => 'Precision tools for quality control department',
                'items' => json_encode([
                    [
                        'sku' => 'TOL-004',
                        'quantity' => 15,
                        'unit_cost' => 22.00,
                        'total_cost' => 330.00
                    ],
                    [
                        'sku' => 'TOL-005',
                        'quantity' => 5,
                        'unit_cost' => 55.00,
                        'total_cost' => 275.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'po_number' => 'PO-2025-010',
                'supplier_id' => 5,
                'supplier_name' => 'Accessory World Inc',
                'order_date' => '2025-08-29',
                'expected_delivery' => '2025-09-12',
                'status' => 'approved',
                'total_amount' => 1250.00,
                'notes' => 'Power supplies and cooling fans - approved',
                'items' => json_encode([
                    [
                        'sku' => 'ACC-003',
                        'quantity' => 50,
                        'unit_cost' => 12.99,
                        'total_cost' => 649.50
                    ],
                    [
                        'sku' => 'ACC-004',
                        'quantity' => 100,
                        'unit_cost' => 4.50,
                        'total_cost' => 450.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subHours(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Historical Orders
            [
                'po_number' => 'PO-2025-011',
                'supplier_id' => 1,
                'supplier_name' => 'ABC Electronics Supply',
                'order_date' => '2025-08-15',
                'expected_delivery' => '2025-08-20',
                'status' => 'completed',
                'total_amount' => 4500.00,
                'notes' => 'LED strips for lighting project - delivered early',
                'items' => json_encode([
                    [
                        'sku' => 'ELC-004',
                        'quantity' => 100,
                        'unit_cost' => 25.00,
                        'total_cost' => 2500.00
                    ],
                    [
                        'sku' => 'ELC-005',
                        'quantity' => 200,
                        'unit_cost' => 3.50,
                        'total_cost' => 700.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(10),
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(5),
            ],
            [
                'po_number' => 'PO-2025-012',
                'supplier_id' => 2,
                'supplier_name' => 'Hardware Solutions Ltd',
                'order_date' => '2025-08-10',
                'expected_delivery' => '2025-08-18',
                'status' => 'completed',
                'total_amount' => 3200.00,
                'notes' => 'Bulk hardware order - completed on schedule',
                'items' => json_encode([
                    [
                        'sku' => 'HWD-001',
                        'quantity' => 100,
                        'unit_cost' => 12.99,
                        'total_cost' => 1299.00
                    ],
                    [
                        'sku' => 'HWD-002',
                        'quantity' => 150,
                        'unit_cost' => 8.50,
                        'total_cost' => 1275.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(15),
                'created_at' => now()->subDays(17),
                'updated_at' => now()->subDays(7),
            ],

            // Large Orders
            [
                'po_number' => 'PO-2025-013',
                'supplier_id' => 3,
                'supplier_name' => 'Global Materials Co',
                'order_date' => '2025-08-05',
                'expected_delivery' => '2025-09-30',
                'status' => 'approved',
                'total_amount' => 25000.00,
                'notes' => 'Major bulk order for Q4 production - approved by director',
                'items' => json_encode([
                    [
                        'sku' => 'RWM-001',
                        'quantity' => 500,
                        'unit_cost' => 12.99,
                        'total_cost' => 6495.00
                    ],
                    [
                        'sku' => 'RWM-002',
                        'quantity' => 2000,
                        'unit_cost' => 2.50,
                        'total_cost' => 5000.00
                    ],
                    [
                        'sku' => 'RWM-003',
                        'quantity' => 1000,
                        'unit_cost' => 8.99,
                        'total_cost' => 8990.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(20),
                'created_at' => now()->subDays(22),
                'updated_at' => now()->subDays(20),
            ],

            // Emergency Orders
            [
                'po_number' => 'PO-2025-014',
                'supplier_id' => 4,
                'supplier_name' => 'Tech Tools & More',
                'order_date' => '2025-08-28',
                'expected_delivery' => '2025-09-02',
                'status' => 'pending',
                'total_amount' => 850.00,
                'notes' => 'URGENT: Emergency tool replacement needed',
                'items' => json_encode([
                    [
                        'sku' => 'TOL-001',
                        'quantity' => 2,
                        'unit_cost' => 45.00,
                        'total_cost' => 90.00
                    ],
                    [
                        'sku' => 'TOL-002',
                        'quantity' => 1,
                        'unit_cost' => 35.00,
                        'total_cost' => 35.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],

            // More Orders for Comprehensive Data
            [
                'po_number' => 'PO-2025-015',
                'supplier_id' => 5,
                'supplier_name' => 'Accessory World Inc',
                'order_date' => '2025-08-26',
                'expected_delivery' => '2025-09-15',
                'status' => 'approved',
                'total_amount' => 2100.00,
                'notes' => 'Project boxes and storage solutions',
                'items' => json_encode([
                    [
                        'sku' => 'ACC-005',
                        'quantity' => 150,
                        'unit_cost' => 6.99,
                        'total_cost' => 1048.50
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'po_number' => 'PO-2025-016',
                'supplier_id' => 1,
                'supplier_name' => 'ABC Electronics Supply',
                'order_date' => '2025-08-24',
                'expected_delivery' => '2025-09-05',
                'status' => 'completed',
                'total_amount' => 1800.00,
                'notes' => 'Breadboards for prototyping lab',
                'items' => json_encode([
                    [
                        'sku' => 'ELC-005',
                        'quantity' => 300,
                        'unit_cost' => 3.50,
                        'total_cost' => 1050.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(4),
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(1),
            ],
            [
                'po_number' => 'PO-2025-017',
                'supplier_id' => 2,
                'supplier_name' => 'Hardware Solutions Ltd',
                'order_date' => '2025-08-22',
                'expected_delivery' => '2025-09-18',
                'status' => 'draft',
                'total_amount' => 4200.00,
                'notes' => 'Draft order for structural components',
                'items' => json_encode([
                    [
                        'sku' => 'HWD-004',
                        'quantity' => 300,
                        'unit_cost' => 8.99,
                        'total_cost' => 2697.00
                    ],
                    [
                        'sku' => 'HWD-005',
                        'quantity' => 2000,
                        'unit_cost' => 0.45,
                        'total_cost' => 900.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'po_number' => 'PO-2025-018',
                'supplier_id' => 3,
                'supplier_name' => 'Global Materials Co',
                'order_date' => '2025-08-18',
                'expected_delivery' => '2025-08-25',
                'status' => 'completed',
                'total_amount' => 1600.00,
                'notes' => 'Heat shrink tubing and jumper wires',
                'items' => json_encode([
                    [
                        'sku' => 'RWM-004',
                        'quantity' => 50,
                        'unit_cost' => 15.00,
                        'total_cost' => 750.00
                    ],
                    [
                        'sku' => 'RWM-005',
                        'quantity' => 100,
                        'unit_cost' => 4.99,
                        'total_cost' => 499.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(8),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(2),
            ],
            [
                'po_number' => 'PO-2025-019',
                'supplier_id' => 4,
                'supplier_name' => 'Tech Tools & More',
                'order_date' => '2025-08-16',
                'expected_delivery' => '2025-09-10',
                'status' => 'approved',
                'total_amount' => 3200.00,
                'notes' => 'Professional tool set for new workshop',
                'items' => json_encode([
                    [
                        'sku' => 'TOL-001',
                        'quantity' => 8,
                        'unit_cost' => 45.00,
                        'total_cost' => 360.00
                    ],
                    [
                        'sku' => 'TOL-002',
                        'quantity' => 5,
                        'unit_cost' => 35.00,
                        'total_cost' => 175.00
                    ],
                    [
                        'sku' => 'TOL-003',
                        'quantity' => 20,
                        'unit_cost' => 18.50,
                        'total_cost' => 370.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(12),
                'created_at' => now()->subDays(14),
                'updated_at' => now()->subDays(12),
            ],
            [
                'po_number' => 'PO-2025-020',
                'supplier_id' => 5,
                'supplier_name' => 'Accessory World Inc',
                'order_date' => '2025-08-12',
                'expected_delivery' => '2025-08-28',
                'status' => 'completed',
                'total_amount' => 950.00,
                'notes' => 'USB cables and MicroSD cards - delivered',
                'items' => json_encode([
                    [
                        'sku' => 'ACC-001',
                        'quantity' => 150,
                        'unit_cost' => 3.99,
                        'total_cost' => 598.50
                    ],
                    [
                        'sku' => 'ACC-002',
                        'quantity' => 25,
                        'unit_cost' => 8.99,
                        'total_cost' => 224.75
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(18),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(3),
            ]
        ];

        DB::table('inventory_purchase_orders')->insert($purchaseOrders);
    }
}