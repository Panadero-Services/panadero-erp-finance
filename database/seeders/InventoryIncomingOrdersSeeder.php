<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryIncomingOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_incoming_orders')->count() > 0) {
            $this->command->info('Inventory incoming orders already exist, skipping...');
            return;
        }

        $incomingOrders = [
            [
                'order_number' => 'PO-2025-001',
                'supplier_id' => 1,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(5),
                'expected_delivery' => now()->addDays(2),
                'actual_delivery' => null,
                'status' => 'confirmed',
                'priority' => 'normal',
                'total_amount' => 1250.00,
                'currency' => 'USD',
                'notes' => 'Regular monthly flour order',
                'created_by' => 1,
                'approved_by' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'order_number' => 'PO-2025-002',
                'supplier_id' => 2,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(3),
                'expected_delivery' => now()->addDays(1),
                'actual_delivery' => null,
                'status' => 'in_transit',
                'priority' => 'high',
                'total_amount' => 850.00,
                'currency' => 'USD',
                'notes' => 'Urgent equipment replacement order',
                'created_by' => 1,
                'approved_by' => 1,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'order_number' => 'PO-2025-003',
                'supplier_id' => 3,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(1),
                'expected_delivery' => now()->addDays(3),
                'actual_delivery' => null,
                'status' => 'pending',
                'priority' => 'normal',
                'total_amount' => 450.00,
                'currency' => 'USD',
                'notes' => 'Seasonal ingredients order',
                'created_by' => 1,
                'approved_by' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'order_number' => 'PO-2025-004',
                'supplier_id' => 1,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(7),
                'expected_delivery' => now()->subDays(1),
                'actual_delivery' => now()->subDays(1),
                'status' => 'delivered',
                'priority' => 'normal',
                'total_amount' => 675.00,
                'currency' => 'USD',
                'notes' => 'Completed delivery - quality check pending',
                'created_by' => 1,
                'approved_by' => 1,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(1),
            ],
            [
                'order_number' => 'PO-2025-005',
                'supplier_id' => 2,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(10),
                'expected_delivery' => now()->subDays(3),
                'actual_delivery' => now()->subDays(2),
                'status' => 'completed',
                'priority' => 'low',
                'total_amount' => 320.00,
                'currency' => 'USD',
                'notes' => 'Completed and quality approved',
                'created_by' => 1,
                'approved_by' => 1,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(2),
            ],
        ];

        DB::table('inventory_incoming_orders')->insert($incomingOrders);
    }
}
