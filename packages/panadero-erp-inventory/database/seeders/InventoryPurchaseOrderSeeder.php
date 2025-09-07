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
            [
                'po_number' => 'PO-2025-001',
                'supplier_id' => 1,
                'supplier_name' => 'ABC Supplies Inc.',
                'order_date' => '2025-08-30',
                'expected_delivery' => '2025-09-05',
                'status' => 'pending',
                'total_amount' => 2599.00,
                'notes' => 'Urgent order for Q4 production',
                'items' => json_encode([
                    [
                        'sku' => 'SKU-001',
                        'quantity' => 100,
                        'unit_cost' => 25.99,
                        'total_cost' => 2599.00
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
                'supplier_name' => 'XYZ Components Ltd.',
                'order_date' => '2025-08-29',
                'expected_delivery' => '2025-09-10',
                'status' => 'approved',
                'total_amount' => 3875.00,
                'notes' => 'Regular monthly order',
                'items' => json_encode([
                    [
                        'sku' => 'SKU-002',
                        'quantity' => 50,
                        'unit_cost' => 15.50,
                        'total_cost' => 775.00
                    ],
                    [
                        'sku' => 'SKU-003',
                        'quantity' => 25,
                        'unit_cost' => 45.00,
                        'total_cost' => 1125.00
                    ],
                    [
                        'sku' => 'SKU-004',
                        'quantity' => 200,
                        'unit_cost' => 8.75,
                        'total_cost' => 1750.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'po_number' => 'PO-2025-003',
                'supplier_id' => 3,
                'supplier_name' => 'Global Materials Co.',
                'order_date' => '2025-08-28',
                'expected_delivery' => '2025-09-15',
                'status' => 'draft',
                'total_amount' => 15000.00,
                'notes' => 'Bulk order for new project',
                'items' => json_encode([
                    [
                        'sku' => 'SKU-004',
                        'quantity' => 1000,
                        'unit_cost' => 8.75,
                        'total_cost' => 8750.00
                    ],
                    [
                        'sku' => 'SKU-001',
                        'quantity' => 200,
                        'unit_cost' => 25.99,
                        'total_cost' => 5198.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'po_number' => 'PO-2025-004',
                'supplier_id' => 4,
                'supplier_name' => 'Tech Tools & More',
                'order_date' => '2025-08-27',
                'expected_delivery' => '2025-09-01',
                'status' => 'completed',
                'total_amount' => 1399.50,
                'notes' => 'Tools for maintenance department',
                'items' => json_encode([
                    [
                        'sku' => 'SKU-003',
                        'quantity' => 20,
                        'unit_cost' => 45.00,
                        'total_cost' => 900.00
                    ],
                    [
                        'sku' => 'SKU-005',
                        'quantity' => 30,
                        'unit_cost' => 12.00,
                        'total_cost' => 360.00
                    ]
                ]),
                'approved_by' => 1,
                'approved_at' => now()->subDays(2),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(1),
            ]
        ];

        DB::table('inventory_purchase_orders')->insert($purchaseOrders);
    }
}