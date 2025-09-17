<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_products')->count() > 0) {
            $this->command->info('Inventory products already exist, skipping...');
            return;
        }

        $products = [
            [
                'name' => 'Premium Artisan Bread',
                'sku' => 'PROD-BREAD-001',
                'description' => 'Handcrafted artisan bread made with organic flour and traditional techniques',
                'category' => 'Bakery',
                'subcategory' => 'Bread',
                'brand' => 'Panadero Artisan',
                'unit' => 'loaf',
                'weight' => 0.5,
                'dimensions' => json_encode(['length' => 25, 'width' => 12, 'height' => 8]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => false,
                'is_bulk' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Organic Whole Wheat Flour',
                'sku' => 'PROD-FLOUR-001',
                'description' => 'Premium organic whole wheat flour, stone-ground for superior quality',
                'category' => 'Raw Materials',
                'subcategory' => 'Flour',
                'brand' => 'Organic Valley',
                'unit' => 'kg',
                'weight' => 1.0,
                'dimensions' => json_encode(['length' => 30, 'width' => 20, 'height' => 5]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => false,
                'is_bulk' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Mixing Bowl Set',
                'sku' => 'PROD-TOOL-001',
                'description' => 'Stainless steel mixing bowl set for professional baking',
                'category' => 'Tools',
                'subcategory' => 'Baking Tools',
                'brand' => 'KitchenPro',
                'unit' => 'set',
                'weight' => 2.5,
                'dimensions' => json_encode(['length' => 35, 'width' => 35, 'height' => 15]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => true,
                'is_bulk' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Extra Virgin Olive Oil',
                'sku' => 'PROD-OIL-001',
                'description' => 'Premium extra virgin olive oil, cold-pressed from Italian olives',
                'category' => 'Raw Materials',
                'subcategory' => 'Oils',
                'brand' => 'Mediterranean Gold',
                'unit' => 'liter',
                'weight' => 1.0,
                'dimensions' => json_encode(['length' => 20, 'width' => 8, 'height' => 25]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => false,
                'is_bulk' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Digital Kitchen Scale',
                'sku' => 'PROD-SCALE-001',
                'description' => 'Precision digital kitchen scale with multiple unit measurements',
                'category' => 'Equipment',
                'subcategory' => 'Measuring Tools',
                'brand' => 'PrecisionPro',
                'unit' => 'unit',
                'weight' => 0.8,
                'dimensions' => json_encode(['length' => 25, 'width' => 20, 'height' => 3]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => true,
                'is_bulk' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Baking Pans Set',
                'sku' => 'PROD-PAN-001',
                'description' => 'Non-stick baking pans set in various sizes',
                'category' => 'Tools',
                'subcategory' => 'Baking Pans',
                'brand' => 'BakeMaster',
                'unit' => 'set',
                'weight' => 3.2,
                'dimensions' => json_encode(['length' => 40, 'width' => 30, 'height' => 8]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => false,
                'is_bulk' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sea Salt',
                'sku' => 'PROD-SALT-001',
                'description' => 'Natural sea salt harvested from pristine waters',
                'category' => 'Raw Materials',
                'subcategory' => 'Seasonings',
                'brand' => 'Ocean Pure',
                'unit' => 'kg',
                'weight' => 1.0,
                'dimensions' => json_encode(['length' => 15, 'width' => 10, 'height' => 8]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => false,
                'is_bulk' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Oven Mitts',
                'sku' => 'PROD-MITT-001',
                'description' => 'Heat-resistant silicone oven mitts for professional use',
                'category' => 'Safety',
                'subcategory' => 'Protective Gear',
                'brand' => 'SafeBake',
                'unit' => 'pair',
                'weight' => 0.3,
                'dimensions' => json_encode(['length' => 30, 'width' => 15, 'height' => 5]),
                'is_active' => true,
                'is_digital' => false,
                'requires_serial' => false,
                'is_bulk' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventory_products')->insert($products);
    }
}
