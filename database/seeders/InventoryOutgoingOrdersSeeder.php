<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryOutgoingOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_outgoing_orders')->count() > 0) {
            $this->command->info('Inventory outgoing orders already exist, skipping...');
            return;
        }

        $outgoingOrders = [
            [
                'order_number' => 'SO-2025-001',
                'customer_id' => 1,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(4),
                'expected_shipment' => now()->addDays(1),
                'actual_shipment' => null,
                'status' => 'confirmed',
                'priority' => 'high',
                'total_amount' => 450.00,
                'currency' => 'USD',
                'shipping_address' => json_encode([
                    'name' => 'John Smith',
                    'company' => 'Smith Bakery',
                    'address' => '123 Main Street',
                    'city' => 'New York',
                    'state' => 'NY',
                    'zip' => '10001',
                    'country' => 'USA'
                ]),
                'notes' => 'Rush order for weekend event',
                'created_by' => 1,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'order_number' => 'SO-2025-002',
                'customer_id' => 2,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(2),
                'expected_shipment' => now()->addDays(2),
                'actual_shipment' => null,
                'status' => 'processing',
                'priority' => 'normal',
                'total_amount' => 275.00,
                'currency' => 'USD',
                'shipping_address' => json_encode([
                    'name' => 'Maria Garcia',
                    'company' => 'Garcia Restaurant',
                    'address' => '456 Oak Avenue',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'zip' => '90210',
                    'country' => 'USA'
                ]),
                'notes' => 'Regular customer order',
                'created_by' => 1,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'order_number' => 'SO-2025-003',
                'customer_id' => 3,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(6),
                'expected_shipment' => now()->subDays(1),
                'actual_shipment' => now()->subDays(1),
                'status' => 'shipped',
                'priority' => 'normal',
                'total_amount' => 180.00,
                'currency' => 'USD',
                'shipping_address' => json_encode([
                    'name' => 'David Johnson',
                    'company' => 'Johnson Cafe',
                    'address' => '789 Pine Street',
                    'city' => 'Chicago',
                    'state' => 'IL',
                    'zip' => '60601',
                    'country' => 'USA'
                ]),
                'notes' => 'Shipped via FedEx - tracking provided',
                'created_by' => 1,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(1),
            ],
            [
                'order_number' => 'SO-2025-004',
                'customer_id' => 4,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(8),
                'expected_shipment' => now()->subDays(3),
                'actual_shipment' => now()->subDays(3),
                'status' => 'delivered',
                'priority' => 'low',
                'total_amount' => 320.00,
                'currency' => 'USD',
                'shipping_address' => json_encode([
                    'name' => 'Sarah Wilson',
                    'company' => 'Wilson Catering',
                    'address' => '321 Elm Street',
                    'city' => 'Miami',
                    'state' => 'FL',
                    'zip' => '33101',
                    'country' => 'USA'
                ]),
                'notes' => 'Delivered and confirmed by customer',
                'created_by' => 1,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(3),
            ],
            [
                'order_number' => 'SO-2025-005',
                'customer_id' => 5,
                'warehouse_id' => 1,
                'order_date' => now()->subDays(1),
                'expected_shipment' => now()->addDays(3),
                'actual_shipment' => null,
                'status' => 'pending',
                'priority' => 'normal',
                'total_amount' => 195.00,
                'currency' => 'USD',
                'shipping_address' => json_encode([
                    'name' => 'Michael Brown',
                    'company' => 'Brown Bakery',
                    'address' => '654 Maple Drive',
                    'city' => 'Seattle',
                    'state' => 'WA',
                    'zip' => '98101',
                    'country' => 'USA'
                ]),
                'notes' => 'New customer - first order',
                'created_by' => 1,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        DB::table('inventory_outgoing_orders')->insert($outgoingOrders);
    }
}