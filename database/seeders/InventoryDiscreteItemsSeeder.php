<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryDiscreteItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_discrete_items')->count() > 0) {
            $this->command->info('Inventory discrete items already exist, skipping...');
            return;
        }

        $items = [
            [
                'sku' => 'ITEM-BREAD-001',
                'name' => 'Premium Artisan Bread',
                'description' => 'Handcrafted artisan bread made with organic flour and traditional techniques',
                'category' => 'Bakery',
                'subcategory' => 'Bread',
                'brand' => 'Panadero Artisan',
                'weight' => 500.000, // grams
                'dimensions' => json_encode(['length' => 25, 'width' => 12, 'height' => 8]), // cm
                'serial_number' => null,
                'barcode' => '1234567890123',
                'packaging_type' => 'individual',
                'units_per_package' => 1,
                'unit_cost' => 3.50,
                'unit_price' => 5.25,
                'package_cost' => 3.50,
                'package_price' => 5.25,
                'min_stock_units' => 50,
                'max_stock_units' => 500,
                'reorder_point_units' => 75,
                'reorder_quantity_units' => 200,
                'status' => 'active',
                'requires_serial' => false,
                'is_fragile' => true,
                'is_hazardous' => false,
                'item_properties' => json_encode([
                    'ingredients' => ['organic flour', 'water', 'salt', 'yeast'],
                    'allergens' => ['gluten'],
                    'shelf_life_days' => 3,
                    'storage_requirements' => 'cool_dry_place'
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'ITEM-TOOL-001',
                'name' => 'Professional Mixing Bowl Set',
                'description' => 'Stainless steel mixing bowl set for professional baking',
                'category' => 'Tools',
                'subcategory' => 'Baking Tools',
                'brand' => 'KitchenPro',
                'weight' => 2500.000, // grams
                'dimensions' => json_encode(['length' => 35, 'width' => 35, 'height' => 15]), // cm
                'serial_number' => 'KB-2025-001',
                'barcode' => '2345678901234',
                'packaging_type' => 'box',
                'units_per_package' => 1,
                'unit_cost' => 45.00,
                'unit_price' => 67.50,
                'package_cost' => 45.00,
                'package_price' => 67.50,
                'min_stock_units' => 2,
                'max_stock_units' => 20,
                'reorder_point_units' => 5,
                'reorder_quantity_units' => 10,
                'status' => 'active',
                'requires_serial' => true,
                'is_fragile' => false,
                'is_hazardous' => false,
                'item_properties' => json_encode([
                    'material' => 'stainless_steel',
                    'finish' => 'brushed',
                    'dishwasher_safe' => true,
                    'warranty_years' => 2
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'ITEM-SCALE-001',
                'name' => 'Digital Kitchen Scale',
                'description' => 'Precision digital kitchen scale with multiple unit measurements',
                'category' => 'Equipment',
                'subcategory' => 'Measuring Tools',
                'brand' => 'PrecisionPro',
                'weight' => 800.000, // grams
                'dimensions' => json_encode(['length' => 25, 'width' => 20, 'height' => 3]), // cm
                'serial_number' => 'PS-2025-001',
                'barcode' => '3456789012345',
                'packaging_type' => 'box',
                'units_per_package' => 1,
                'unit_cost' => 25.00,
                'unit_price' => 37.50,
                'package_cost' => 25.00,
                'package_price' => 37.50,
                'min_stock_units' => 1,
                'max_stock_units' => 10,
                'reorder_point_units' => 2,
                'reorder_quantity_units' => 5,
                'status' => 'active',
                'requires_serial' => true,
                'is_fragile' => true,
                'is_hazardous' => false,
                'item_properties' => json_encode([
                    'max_weight' => '5000g',
                    'precision' => '1g',
                    'battery_type' => 'AAA',
                    'display_type' => 'LCD'
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'ITEM-PAN-001',
                'name' => 'Baking Pans Set',
                'description' => 'Non-stick baking pans set in various sizes',
                'category' => 'Tools',
                'subcategory' => 'Baking Pans',
                'brand' => 'BakeMaster',
                'weight' => 3200.000, // grams
                'dimensions' => json_encode(['length' => 40, 'width' => 30, 'height' => 8]), // cm
                'serial_number' => null,
                'barcode' => '4567890123456',
                'packaging_type' => 'box',
                'units_per_package' => 1,
                'unit_cost' => 18.50,
                'unit_price' => 27.75,
                'package_cost' => 18.50,
                'package_price' => 27.75,
                'min_stock_units' => 5,
                'max_stock_units' => 30,
                'reorder_point_units' => 8,
                'reorder_quantity_units' => 15,
                'status' => 'active',
                'requires_serial' => false,
                'is_fragile' => false,
                'is_hazardous' => false,
                'item_properties' => json_encode([
                    'coating' => 'non_stick',
                    'material' => 'aluminum',
                    'dishwasher_safe' => true,
                    'sizes' => ['9x13', '8x8', 'muffin']
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'ITEM-MITT-001',
                'name' => 'Professional Oven Mitts',
                'description' => 'Heat-resistant silicone oven mitts for professional use',
                'category' => 'Safety',
                'subcategory' => 'Protective Gear',
                'brand' => 'SafeBake',
                'weight' => 300.000, // grams
                'dimensions' => json_encode(['length' => 30, 'width' => 15, 'height' => 5]), // cm
                'serial_number' => null,
                'barcode' => '5678901234567',
                'packaging_type' => 'pair',
                'units_per_package' => 1,
                'unit_cost' => 12.00,
                'unit_price' => 18.00,
                'package_cost' => 12.00,
                'package_price' => 18.00,
                'min_stock_units' => 5,
                'max_stock_units' => 50,
                'reorder_point_units' => 10,
                'reorder_quantity_units' => 20,
                'status' => 'active',
                'requires_serial' => false,
                'is_fragile' => false,
                'is_hazardous' => false,
                'item_properties' => json_encode([
                    'material' => 'silicone',
                    'max_temperature' => '500F',
                    'color' => 'red',
                    'size' => 'large'
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventory_discrete_items')->insert($items);
    }
}