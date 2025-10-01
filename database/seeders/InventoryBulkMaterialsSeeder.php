<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryBulkMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_bulk_materials')->count() > 0) {
            $this->command->info('Inventory bulk materials already exist, skipping...');
            return;
        }

        $materials = [
            [
                'sku' => 'BULK-FLOUR-001',
                'name' => 'Organic Whole Wheat Flour',
                'description' => 'Premium organic whole wheat flour, stone-ground for superior quality',
                'category' => 'Raw Materials',
                'subcategory' => 'Flour',
                'brand' => 'Organic Valley',
                'density' => 0.593, // kg/m³
                'moisture_content' => 12.5,
                'purity' => 99.8,
                'particle_size' => 'fine',
                'storage_requirements' => 'dry_cool_place',
                'batch_number' => 'BATCH-2025-001',
                'expiry_date' => now()->addMonths(12),
                'cost_per_kg' => 2.25,
                'price_per_kg' => 3.38,
                'cost_per_ton' => 2250.00,
                'price_per_ton' => 3380.00,
                'min_stock_kg' => 50.0000,
                'max_stock_kg' => 500.0000,
                'reorder_point_kg' => 75.0000,
                'reorder_quantity_kg' => 200.0000,
                'status' => 'active',
                'requires_batch_tracking' => true,
                'is_hazardous' => false,
                'is_perishable' => true,
                'bulk_properties' => json_encode([
                    'protein_content' => '12%',
                    'origin' => 'Canada',
                    'certification' => 'Organic',
                    'gluten_content' => 'high',
                    'shelf_life_months' => 12
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'BULK-SALT-001',
                'name' => 'Sea Salt',
                'description' => 'Natural sea salt harvested from pristine waters',
                'category' => 'Raw Materials',
                'subcategory' => 'Seasonings',
                'brand' => 'Ocean Pure',
                'density' => 1.200, // kg/m³
                'moisture_content' => 0.5,
                'purity' => 99.9,
                'particle_size' => 'medium',
                'storage_requirements' => 'dry_place',
                'batch_number' => 'BATCH-2025-002',
                'expiry_date' => now()->addYears(5),
                'cost_per_kg' => 1.25,
                'price_per_kg' => 1.88,
                'cost_per_ton' => 1250.00,
                'price_per_ton' => 1880.00,
                'min_stock_kg' => 20.0000,
                'max_stock_kg' => 150.0000,
                'reorder_point_kg' => 30.0000,
                'reorder_quantity_kg' => 50.0000,
                'status' => 'active',
                'requires_batch_tracking' => true,
                'is_hazardous' => false,
                'is_perishable' => false,
                'bulk_properties' => json_encode([
                    'mineral_content' => 'high',
                    'iodine_added' => false,
                    'color' => 'white',
                    'crystal_size' => 'medium',
                    'shelf_life_years' => 5
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'BULK-SUGAR-001',
                'name' => 'Granulated Sugar',
                'description' => 'Pure granulated sugar for baking and cooking',
                'category' => 'Raw Materials',
                'subcategory' => 'Sweeteners',
                'brand' => 'SweetCane',
                'density' => 0.850, // kg/m³
                'moisture_content' => 0.1,
                'purity' => 99.9,
                'particle_size' => 'fine',
                'storage_requirements' => 'dry_cool_place',
                'batch_number' => 'BATCH-2025-003',
                'expiry_date' => now()->addYears(3),
                'cost_per_kg' => 1.80,
                'price_per_kg' => 2.70,
                'cost_per_ton' => 1800.00,
                'price_per_ton' => 2700.00,
                'min_stock_kg' => 100.0000,
                'max_stock_kg' => 1000.0000,
                'reorder_point_kg' => 150.0000,
                'reorder_quantity_kg' => 500.0000,
                'status' => 'active',
                'requires_batch_tracking' => true,
                'is_hazardous' => false,
                'is_perishable' => false,
                'bulk_properties' => json_encode([
                    'sweetness_level' => 'high',
                    'color' => 'white',
                    'crystal_size' => 'fine',
                    'shelf_life_years' => 3,
                    'source' => 'cane'
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventory_bulk_materials')->insert($materials);
    }
}