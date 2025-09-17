<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryStockItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_stock_items')->count() > 0) {
            $this->command->info('Inventory stock items already exist, skipping...');
            return;
        }

        $stockItems = [
            [
                'name' => 'Premium Bread Loaf',
                'sku' => 'SKU-001',
                'description' => 'Artisan bread made with organic flour',
                'stock_type_id' => 1, // Items
                'category' => 'Bakery',
                'subcategory' => 'Bread',
                'unit' => 'units',
                'min_stock' => 10.0000,
                'max_stock' => 500.0000,
                'current_stock' => 80.0000,
                'unit_cost' => 10.50,
                'unit_price' => 15.75,
                'warehouse_id' => 1,
                'zone' => 'A1',
                'shelf' => 'A1-S1',
                'bin' => 'A1-S1-B1',
                'status' => 'active',
                'type_specific_properties' => json_encode([
                    'weight' => '500g',
                    'ingredients' => ['organic flour', 'water', 'salt', 'yeast'],
                    'allergens' => ['gluten']
                ]),
                'metadata' => json_encode([
                    'supplier_id' => 1,
                    'reorder_point' => 25.0000,
                    'reserved_stock' => 5.0000
                ]),
                'created_by' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'name' => 'Organic Flour',
                'sku' => 'SKU-002',
                'description' => 'High-quality organic wheat flour',
                'stock_type_id' => 2, // Bulk
                'category' => 'Raw Materials',
                'subcategory' => 'Flour',
                'unit' => 'kg',
                'min_stock' => 50.0000,
                'max_stock' => 1000.0000,
                'current_stock' => 35.0000,
                'unit_cost' => 5.25,
                'unit_price' => 7.50,
                'warehouse_id' => 1,
                'zone' => 'B1',
                'shelf' => 'B1-S1',
                'bin' => 'B1-S1-B1',
                'status' => 'active',
                'type_specific_properties' => json_encode([
                    'protein_content' => '12%',
                    'origin' => 'Canada',
                    'certification' => 'Organic'
                ]),
                'metadata' => json_encode([
                    'supplier_id' => 2,
                    'reorder_point' => 100.0000,
                    'reserved_stock' => 0.0000
                ]),
                'created_by' => 1,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'name' => 'Maintenance Tools',
                'sku' => 'SKU-003',
                'description' => 'Essential tools for equipment maintenance',
                'stock_type_id' => 1, // Items
                'category' => 'Tools',
                'subcategory' => 'Maintenance',
                'unit' => 'units',
                'min_stock' => 5.0000,
                'max_stock' => 50.0000,
                'current_stock' => 20.0000,
                'unit_cost' => 25.00,
                'unit_price' => 35.00,
                'warehouse_id' => 1,
                'zone' => 'C1',
                'shelf' => 'C1-S1',
                'bin' => 'C1-S1-B1',
                'status' => 'active',
                'type_specific_properties' => json_encode([
                    'tool_type' => 'wrench',
                    'size' => '10mm',
                    'material' => 'chrome vanadium'
                ]),
                'metadata' => json_encode([
                    'supplier_id' => 3,
                    'reorder_point' => 10.0000,
                    'reserved_stock' => 0.0000
                ]),
                'created_by' => 1,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'name' => 'Cooking Oil',
                'sku' => 'SKU-004',
                'description' => 'Vegetable oil for cooking and baking',
                'stock_type_id' => 3, // Liquids
                'category' => 'Raw Materials',
                'subcategory' => 'Oils',
                'unit' => 'liters',
                'min_stock' => 20.0000,
                'max_stock' => 500.0000,
                'current_stock' => 150.0000,
                'unit_cost' => 2.50,
                'unit_price' => 3.75,
                'warehouse_id' => 1,
                'zone' => 'D1',
                'shelf' => 'D1-S1',
                'bin' => 'D1-S1-B1',
                'status' => 'active',
                'type_specific_properties' => json_encode([
                    'oil_type' => 'vegetable',
                    'smoke_point' => '204°C',
                    'container_type' => 'plastic_bottle'
                ]),
                'metadata' => json_encode([
                    'supplier_id' => 2,
                    'reorder_point' => 50.0000,
                    'reserved_stock' => 0.0000
                ]),
                'created_by' => 1,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'name' => 'Tool Accessories',
                'sku' => 'SKU-005',
                'description' => 'Replacement parts and accessories for tools',
                'stock_type_id' => 1, // Items
                'category' => 'Accessories',
                'subcategory' => 'Tool Parts',
                'unit' => 'units',
                'min_stock' => 10.0000,
                'max_stock' => 100.0000,
                'current_stock' => 25.0000,
                'unit_cost' => 8.75,
                'unit_price' => 12.50,
                'warehouse_id' => 1,
                'zone' => 'C2',
                'shelf' => 'C2-S1',
                'bin' => 'C2-S1-B1',
                'status' => 'active',
                'type_specific_properties' => json_encode([
                    'compatible_tool' => 'wrench',
                    'part_type' => 'replacement_handle',
                    'color' => 'black'
                ]),
                'metadata' => json_encode([
                    'supplier_id' => 3,
                    'reorder_point' => 20.0000,
                    'reserved_stock' => 0.0000
                ]),
                'created_by' => 1,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        DB::table('inventory_stock_items')->insert($stockItems);
    }
}
