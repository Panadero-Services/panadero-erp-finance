<?php
// 2025_09_17_202120_seed_erp_lookup_data.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Seed status
        DB::table('erp_status')->insert([
            ['name' => 'Pending', 'code' => 'pending', 'sort_order' => 1, 'color' => 'yellow', 'description' => 'Initial status', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Released', 'code' => 'released', 'sort_order' => 2, 'color' => 'blue', 'description' => 'Approved and ready', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Active', 'code' => 'active', 'sort_order' => 3, 'color' => 'green', 'description' => 'Currently active', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finished', 'code' => 'finished', 'sort_order' => 4, 'color' => 'gray', 'description' => 'Completed', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Archived', 'code' => 'archived', 'sort_order' => 5, 'color' => 'black', 'description' => 'Archived', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed units
        DB::table('erp_units')->insert([
            ['name' => 'Piece', 'symbol' => 'pcs', 'is_decimal' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'is_decimal' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Liter', 'symbol' => 'L', 'is_decimal' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meter', 'symbol' => 'm', 'is_decimal' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Box', 'symbol' => 'box', 'is_decimal' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed product types
        DB::table('erp_product_types')->insert([
            ['name' => 'normal', 'description' => 'Standard discrete items that are counted individually', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'bulk', 'description' => 'Bulk materials measured by weight or volume', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'liquid', 'description' => 'Liquid materials measured by volume', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'service', 'description' => 'Services or intangible products', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed analyse properties
        DB::table('erp_analyse_properties')->insert([
            ['name' => 'Moisture Content', 'unit' => '%', 'data_type' => 'number', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pH Level', 'unit' => 'pH', 'data_type' => 'number', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Temperature', 'unit' => '°C', 'data_type' => 'number', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Purity', 'unit' => '%', 'data_type' => 'number', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Viscosity', 'unit' => 'cP', 'data_type' => 'number', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('erp_units')->truncate();
        DB::table('erp_product_types')->truncate();
        DB::table('erp_status')->truncate();
        DB::table('erp_analyse_properties')->truncate();
    }
};