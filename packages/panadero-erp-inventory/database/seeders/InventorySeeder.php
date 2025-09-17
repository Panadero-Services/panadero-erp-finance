<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Seed Products
        DB::table('inventory_products')->insert([
            [
                'sku' => 'LAPTOP-DELL-001',
                'name' => 'Dell Laptop XPS 13',
                'description' => 'High-performance business laptop',
                'category' => 'Electronics',
                'subcategory' => 'Computers',
                'group' => 'IT Equipment',
                'type' => 'items',
                'unit' => 'units',
                'specifications' => json_encode([
                    'processor' => 'Intel i7',
                    'memory' => '16GB RAM',
                    'storage' => '512GB SSD',
                    'weight' => '1.2kg'
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'sku' => 'MONITOR-SAMSUNG-001',
                'name' => 'Samsung Monitor 24"',
                'description' => '24-inch LED monitor',
                'category' => 'Electronics',
                'subcategory' => 'Monitors',
                'group' => 'IT Equipment',
                'type' => 'items',
                'unit' => 'units',
                'specifications' => json_encode([
                    'size' => '24 inch',
                    'resolution' => '1920x1080',
                    'refresh_rate' => '60Hz',
                    'panel_type' => 'LED'
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'sku' => 'GRAIN-WHEAT-001',
                'name' => 'Wheat Grain Premium',
                'description' => 'High-quality wheat grain for production',
                'category' => 'Raw Materials',
                'subcategory' => 'Grains',
                'group' => 'Raw Materials',
                'type' => 'bulk',
                'unit' => 'kg',
                'specifications' => json_encode([
                    'moisture' => '12%',
                    'protein' => '14%',
                    'origin' => 'Canada',
                    'grade' => 'A'
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'sku' => 'OIL-MOTOR-001',
                'name' => 'Motor Oil 5W-30',
                'description' => 'Synthetic motor oil for automotive use',
                'category' => 'Automotive',
                'subcategory' => 'Oils',
                'group' => 'Automotive',
                'type' => 'liquids',
                'unit' => 'liters',
                'specifications' => json_encode([
                    'viscosity' => '5W-30',
                    'type' => 'Synthetic',
                    'capacity' => '4L',
                    'temperature_range' => '-30°C to 40°C'
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Stock Levels
        DB::table('inventory_stock_levels')->insert([
            [
                'product_id' => 1,
                'sku' => 'LAPTOP-DELL-001',
                'name' => 'Dell Laptop XPS 13',
                'current_stock' => 25,
                'min_stock' => 5,
                'max_stock' => 100,
                'unit' => 'units',
                'location' => 'Main Warehouse',
                'zone' => 'A',
                'shelf' => 'A1',
                'bin' => 'A1-01',
                'status' => 'normal',
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'sku' => 'MONITOR-SAMSUNG-001',
                'name' => 'Samsung Monitor 24"',
                'current_stock' => 15,
                'min_stock' => 3,
                'max_stock' => 50,
                'unit' => 'units',
                'location' => 'Main Warehouse',
                'zone' => 'A',
                'shelf' => 'A2',
                'bin' => 'A2-01',
                'status' => 'normal',
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 3,
                'sku' => 'GRAIN-WHEAT-001',
                'name' => 'Wheat Grain Premium',
                'current_stock' => 1500,
                'min_stock' => 200,
                'max_stock' => 5000,
                'unit' => 'kg',
                'location' => 'Bulk Storage',
                'zone' => 'Silo A',
                'shelf' => null,
                'bin' => null,
                'status' => 'normal',
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 4,
                'sku' => 'OIL-MOTOR-001',
                'name' => 'Motor Oil 5W-30',
                'current_stock' => 200,
                'min_stock' => 50,
                'max_stock' => 1000,
                'unit' => 'liters',
                'location' => 'Liquid Storage',
                'zone' => 'Tank 1',
                'shelf' => null,
                'bin' => null,
                'status' => 'normal',
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Partners
        DB::table('inventory_partners')->insert([
            [
                'name' => 'Dell Technologies',
                'type' => 'vendor',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@dell.com',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street, Austin, TX 78701',
                'payment_terms' => 'Net 30',
                'rating' => 4.5,
                'total_orders' => 25,
                'total_value' => 150000,
                'last_order_date' => '2025-01-10',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GrainCorp',
                'type' => 'vendor',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah.johnson@graincorp.com',
                'phone' => '+1-555-0456',
                'address' => '456 Farm Road, Kansas City, KS 66101',
                'payment_terms' => 'Net 15',
                'rating' => 4.2,
                'total_orders' => 12,
                'total_value' => 48000,
                'last_order_date' => '2025-01-12',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ABC Corporation',
                'type' => 'customer',
                'contact_person' => 'Mike Wilson',
                'email' => 'mike.wilson@abccorp.com',
                'phone' => '+1-555-0789',
                'address' => '789 Business Blvd, New York, NY 10001',
                'credit_limit' => 50000,
                'rating' => 4.8,
                'total_orders' => 18,
                'total_value' => 75000,
                'last_order_date' => '2025-01-12',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'FastLogistics Inc',
                'type' => 'forwarder',
                'contact_person' => 'Lisa Brown',
                'email' => 'lisa.brown@fastlogistics.com',
                'phone' => '+1-555-0321',
                'address' => '321 Logistics Lane, Chicago, IL 60601',
                'services' => json_encode(['Ground', 'Air', 'Sea']),
                'rating' => 4.3,
                'total_orders' => 45,
                'total_value' => 0,
                'on_time_delivery' => 92,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Incoming Orders
        DB::table('inventory_incoming_orders')->insert([
            [
                'order_number' => 'PO-2025-001',
                'supplier' => 'Dell Technologies',
                'order_date' => '2025-01-10',
                'expected_date' => '2025-01-20',
                'status' => 'planned',
                'total_value' => 75000,
                'currency' => 'USD',
                'notes' => 'Bulk order for Q1 inventory',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'order_number' => 'PO-2025-002',
                'supplier' => 'GrainCorp',
                'order_date' => '2025-01-12',
                'expected_date' => '2025-01-18',
                'status' => 'unloading',
                'total_value' => 4000,
                'currency' => 'USD',
                'notes' => 'Monthly grain supply',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Incoming Order Items
        DB::table('inventory_incoming_order_items')->insert([
            [
                'order_id' => 1,
                'sku' => 'LAPTOP-DELL-001',
                'name' => 'Dell Laptop XPS 13',
                'quantity' => 50,
                'unit' => 'units',
                'received' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'order_id' => 2,
                'sku' => 'GRAIN-WHEAT-001',
                'name' => 'Wheat Grain Premium',
                'quantity' => 2000,
                'unit' => 'kg',
                'received' => 1500,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Outgoing Orders
        DB::table('inventory_outgoing_orders')->insert([
            [
                'order_number' => 'SO-2025-001',
                'customer' => 'ABC Corporation',
                'order_date' => '2025-01-12',
                'expected_date' => '2025-01-18',
                'status' => 'planned',
                'total_value' => 15000,
                'currency' => 'USD',
                'shipping_address' => '123 Business St, City, State 12345',
                'notes' => 'Urgent delivery required',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'order_number' => 'SO-2025-002',
                'customer' => 'XYZ Manufacturing',
                'order_date' => '2025-01-14',
                'expected_date' => '2025-01-20',
                'status' => 'shipping',
                'total_value' => 1000,
                'currency' => 'USD',
                'shipping_address' => '456 Industrial Ave, City, State 12345',
                'notes' => 'Regular monthly delivery',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Outgoing Order Items
        DB::table('inventory_outgoing_order_items')->insert([
            [
                'order_id' => 1,
                'sku' => 'LAPTOP-DELL-001',
                'name' => 'Dell Laptop XPS 13',
                'quantity' => 10,
                'unit' => 'units',
                'shipped' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'order_id' => 2,
                'sku' => 'GRAIN-WHEAT-001',
                'name' => 'Wheat Grain Premium',
                'quantity' => 500,
                'unit' => 'kg',
                'shipped' => 500,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Closeout
        DB::table('inventory_closeouts')->insert([
            [
                'period' => '2025-01',
                'type' => 'monthly',
                'start_date' => '2025-01-01',
                'end_date' => '2025-01-31',
                'status' => 'completed',
                'total_start_value' => 150000,
                'total_incoming_value' => 75000,
                'total_outgoing_value' => 67500,
                'total_adjustments_value' => 0,
                'total_end_value' => 157500,
                'total_variance' => 0,
                'created_by' => 1,
                'completed_at' => '2025-02-01T00:00:00Z',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seed Closeout Items
        DB::table('inventory_closeout_items')->insert([
            [
                'closeout_id' => 1,
                'sku' => 'LAPTOP-DELL-001',
                'name' => 'Dell Laptop XPS 13',
                'start_stock' => 20,
                'incoming' => 50,
                'outgoing' => 45,
                'adjustments' => 0,
                'end_stock' => 25,
                'variance' => 0,
                'status' => 'balanced',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'closeout_id' => 1,
                'sku' => 'GRAIN-WHEAT-001',
                'name' => 'Wheat Grain Premium',
                'start_stock' => 1000,
                'incoming' => 2000,
                'outgoing' => 1500,
                'adjustments' => 0,
                'end_stock' => 1500,
                'variance' => 0,
                'status' => 'balanced',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
