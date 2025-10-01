<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ErpLookupDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏭 Seeding ERP lookup data...');

        // Seed status
        if (Schema::hasTable('status')) {
            $this->command->info('📋 Seeding status data...');
            $statusData = [
                ['name' => 'Pending', 'code' => 'pending', 'sort_order' => 1, 'color' => 'yellow', 'description' => 'Initial status', 'is_active' => true],
                ['name' => 'Released', 'code' => 'released', 'sort_order' => 2, 'color' => 'blue', 'description' => 'Approved and ready', 'is_active' => true],
                ['name' => 'Active', 'code' => 'active', 'sort_order' => 3, 'color' => 'green', 'description' => 'Currently active', 'is_active' => true],
                ['name' => 'Finished', 'code' => 'finished', 'sort_order' => 4, 'color' => 'gray', 'description' => 'Completed', 'is_active' => true],
                ['name' => 'Archived', 'code' => 'archived', 'sort_order' => 5, 'color' => 'black', 'description' => 'Archived', 'is_active' => true],
            ];

            foreach ($statusData as $status) {
                DB::table('status')->updateOrInsert(
                    ['code' => $status['code']],
                    array_merge($status, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        } else {
            $this->command->warn('⚠️  Status table does not exist. Skipping status data seeding.');
        }

        // Seed units
        if (Schema::hasTable('units')) {
            $this->command->info('📏 Seeding units data...');
            $unitsData = [
                ['name' => 'unit', 'symbol' => 'pcs', 'type' => 'unit', 'conversion_factor' => 1, 'base_unit' => null, 'is_active' => true],
                ['name' => 'Tons', 'symbol' => 'T', 'type' => 'weight', 'conversion_factor' => 1000, 'base_unit' => 'kg', 'is_active' => true],
                ['name' => 'Cubic Meters', 'symbol' => 'M3', 'type' => 'volume', 'conversion_factor' => 1000, 'base_unit' => 'liters', 'is_active' => true],
                ['name' => 'Kilograms', 'symbol' => 'kg', 'type' => 'weight', 'conversion_factor' => 1, 'base_unit' => null, 'is_active' => true],
                ['name' => 'Liters', 'symbol' => 'L', 'type' => 'volume', 'conversion_factor' => 1, 'base_unit' => null, 'is_active' => true],
            ];

            foreach ($unitsData as $unit) {
                DB::table('units')->updateOrInsert(
                    ['symbol' => $unit['symbol']],
                    array_merge($unit, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        } else {
            $this->command->warn('⚠️  Units table does not exist. Skipping units data seeding.');
        }

        // Seed product types
        if (Schema::hasTable('product_types')) {
            $this->command->info('📦 Seeding product types data...');
            $productTypesData = [
                ['name' => 'Bulk', 'code' => 'bulk', 'description' => 'Granular materials', 'is_active' => true],
                ['name' => 'Article', 'code' => 'article', 'description' => 'Discrete items', 'is_active' => true],
                ['name' => 'Service', 'code' => 'service', 'description' => 'Services', 'is_active' => true],
                ['name' => 'Package', 'code' => 'package', 'description' => 'Packaged goods', 'is_active' => true],
            ];

            foreach ($productTypesData as $productType) {
                DB::table('product_types')->updateOrInsert(
                    ['code' => $productType['code']],
                    array_merge($productType, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        } else {
            $this->command->warn('⚠️  Product types table does not exist. Skipping product types data seeding.');
        }

        // Seed analyse properties
        if (Schema::hasTable('analyse_properties')) {
            $this->command->info('🔬 Seeding analyse properties data...');
            $analysePropertiesData = [
                ['name' => 'Moisture Content', 'unit' => '%', 'data_type' => 'number', 'is_active' => true],
                ['name' => 'pH Level', 'unit' => 'pH', 'data_type' => 'number', 'is_active' => true],
                ['name' => 'Temperature', 'unit' => '°C', 'data_type' => 'number', 'is_active' => true],
                ['name' => 'Purity', 'unit' => '%', 'data_type' => 'number', 'is_active' => true],
                ['name' => 'Viscosity', 'unit' => 'cP', 'data_type' => 'number', 'is_active' => true],
            ];

            foreach ($analysePropertiesData as $property) {
                DB::table('analyse_properties')->updateOrInsert(
                    ['name' => $property['name']],
                    array_merge($property, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        } else {
            $this->command->warn('⚠️  Analyse properties table does not exist. Skipping analyse properties data seeding.');
        }

        $this->command->info('✅ ERP lookup data seeded successfully!');
    }
}
