<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryLiquidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_liquids')->count() > 0) {
            $this->command->info('Inventory liquids already exist, skipping...');
            return;
        }

        $liquids = [
            [
                'sku' => 'LIQ-OIL-001',
                'name' => 'Extra Virgin Olive Oil',
                'description' => 'Premium extra virgin olive oil, cold-pressed from Italian olives',
                'category' => 'Raw Materials',
                'subcategory' => 'Oils',
                'brand' => 'Mediterranean Gold',
                'viscosity' => 84.5, // cP
                'ph_level' => 5.2,
                'temperature_range' => '15-25°C',
                'container_type' => 'bottle',
                'container_size_liters' => 1.0,
                'handling_requirements' => 'store_upright_avoid_light',
                'lot_number' => 'LOT-2025-001',
                'expiry_date' => now()->addMonths(24),
                'cost_per_liter' => 8.75,
                'price_per_liter' => 13.13,
                'cost_per_gallon' => 33.12,
                'price_per_gallon' => 49.68,
                'min_stock_liters' => 10.0000,
                'max_stock_liters' => 100.0000,
                'reorder_point_liters' => 15.0000,
                'reorder_quantity_liters' => 50.0000,
                'status' => 'active',
                'requires_lot_tracking' => true,
                'is_hazardous' => false,
                'is_perishable' => true,
                'requires_refrigeration' => false,
                'liquid_properties' => json_encode([
                    'acidity_level' => '0.3%',
                    'color' => 'golden_green',
                    'flavor_profile' => 'fruity_peppery',
                    'smoke_point' => '204°C',
                    'shelf_life_months' => 24
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'LIQ-VINEGAR-001',
                'name' => 'Balsamic Vinegar',
                'description' => 'Aged balsamic vinegar from Modena, Italy',
                'category' => 'Raw Materials',
                'subcategory' => 'Vinegars',
                'brand' => 'Modena Gold',
                'viscosity' => 2.1, // cP
                'ph_level' => 2.8,
                'temperature_range' => '15-25°C',
                'container_type' => 'bottle',
                'container_size_liters' => 0.5,
                'handling_requirements' => 'store_upright_avoid_light',
                'lot_number' => 'LOT-2025-002',
                'expiry_date' => now()->addYears(10),
                'cost_per_liter' => 25.00,
                'price_per_liter' => 37.50,
                'cost_per_gallon' => 94.64,
                'price_per_gallon' => 141.96,
                'min_stock_liters' => 5.0000,
                'max_stock_liters' => 50.0000,
                'reorder_point_liters' => 8.0000,
                'reorder_quantity_liters' => 20.0000,
                'status' => 'active',
                'requires_lot_tracking' => true,
                'is_hazardous' => false,
                'is_perishable' => false,
                'requires_refrigeration' => false,
                'liquid_properties' => json_encode([
                    'acidity_level' => '6%',
                    'color' => 'dark_brown',
                    'flavor_profile' => 'sweet_tart',
                    'aging_years' => 12,
                    'shelf_life_years' => 10
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'LIQ-MILK-001',
                'name' => 'Fresh Whole Milk',
                'description' => 'Fresh whole milk for baking and cooking',
                'category' => 'Raw Materials',
                'subcategory' => 'Dairy',
                'brand' => 'Farm Fresh',
                'viscosity' => 1.5, // cP
                'ph_level' => 6.7,
                'temperature_range' => '2-8°C',
                'container_type' => 'carton',
                'container_size_liters' => 1.0,
                'handling_requirements' => 'refrigerate_immediately',
                'lot_number' => 'LOT-2025-003',
                'expiry_date' => now()->addDays(7),
                'cost_per_liter' => 2.50,
                'price_per_liter' => 3.75,
                'cost_per_gallon' => 9.46,
                'price_per_gallon' => 14.19,
                'min_stock_liters' => 20.0000,
                'max_stock_liters' => 100.0000,
                'reorder_point_liters' => 30.0000,
                'reorder_quantity_liters' => 50.0000,
                'status' => 'active',
                'requires_lot_tracking' => true,
                'is_hazardous' => false,
                'is_perishable' => true,
                'requires_refrigeration' => true,
                'liquid_properties' => json_encode([
                    'fat_content' => '3.25%',
                    'protein_content' => '3.4%',
                    'color' => 'white',
                    'pasteurized' => true,
                    'shelf_life_days' => 7
                ]),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventory_liquids')->insert($liquids);
    }
}