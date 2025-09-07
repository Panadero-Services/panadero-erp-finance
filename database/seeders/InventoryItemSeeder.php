<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'sku' => 'SKU-001',
                'name' => 'Widget A',
                'category' => 'Electronics',
                'description' => 'High-quality electronic widget for industrial use',
                'current_stock' => 150,
                'min_stock' => 50,
                'max_stock' => 500,
                'unit_cost' => 25.99,
                'unit_price' => 39.99,
                'location' => 'Warehouse A - Shelf 1',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'SKU-002',
                'name' => 'Component B',
                'category' => 'Hardware',
                'description' => 'Essential hardware component for assembly',
                'current_stock' => 75,
                'min_stock' => 25,
                'max_stock' => 200,
                'unit_cost' => 15.50,
                'unit_price' => 24.99,
                'location' => 'Warehouse B - Shelf 3',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'SKU-003',
                'name' => 'Tool C',
                'category' => 'Tools',
                'description' => 'Professional grade tool for maintenance',
                'current_stock' => 30,
                'min_stock' => 10,
                'max_stock' => 100,
                'unit_cost' => 45.00,
                'unit_price' => 69.99,
                'location' => 'Warehouse A - Shelf 2',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'SKU-004',
                'name' => 'Material D',
                'category' => 'Raw Materials',
                'description' => 'Raw material for production processes',
                'current_stock' => 500,
                'min_stock' => 100,
                'max_stock' => 1000,
                'unit_cost' => 8.75,
                'unit_price' => 12.50,
                'location' => 'Warehouse C - Bulk Storage',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'SKU-005',
                'name' => 'Accessory E',
                'category' => 'Accessories',
                'description' => 'Optional accessory for enhanced functionality',
                'current_stock' => 5,
                'min_stock' => 20,
                'max_stock' => 50,
                'unit_cost' => 12.00,
                'unit_price' => 18.99,
                'location' => 'Warehouse B - Shelf 1',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'SKU-006',
                'name' => 'Discontinued Item',
                'category' => 'Legacy',
                'description' => 'Legacy item being phased out',
                'current_stock' => 10,
                'min_stock' => 0,
                'max_stock' => 0,
                'unit_cost' => 5.00,
                'unit_price' => 8.99,
                'location' => 'Warehouse A - Clearance',
                'status' => 'discontinued',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('inventory_items')->insert($items);
    }
}
