<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // items, bulk, liquids
            $table->string('display_name'); // Discrete Items, Bulk Materials, Liquids
            $table->text('description')->nullable();
            $table->string('unit_type'); // discrete, weight, volume
            $table->string('default_unit'); // units, kg, liters
            $table->json('allowed_units')->nullable(); // ["units", "pcs", "boxes"]
            $table->json('properties_schema')->nullable(); // JSON schema for type-specific properties
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'sort_order']);
        });
        
        // Insert default stock types
        DB::table('inventory_stock_types')->insert([
            [
                'name' => 'items',
                'display_name' => 'Discrete Items',
                'description' => 'Individual countable items like products, parts, components',
                'unit_type' => 'discrete',
                'default_unit' => 'units',
                'allowed_units' => json_encode(['units', 'pcs', 'boxes', 'pallets']),
                'properties_schema' => json_encode([
                    'dimensions' => ['length', 'width', 'height'],
                    'weight' => 'decimal',
                    'serial_number' => 'boolean',
                    'barcode' => 'string'
                ]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'bulk',
                'display_name' => 'Bulk Materials',
                'description' => 'Materials measured by weight like raw materials, chemicals, powders',
                'unit_type' => 'weight',
                'default_unit' => 'kg',
                'allowed_units' => json_encode(['kg', 'g', 'tons', 'lbs', 'oz']),
                'properties_schema' => json_encode([
                    'density' => 'decimal',
                    'moisture_content' => 'decimal',
                    'purity' => 'decimal',
                    'batch_number' => 'string'
                ]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'liquids',
                'display_name' => 'Liquids',
                'description' => 'Liquids measured by volume like oils, chemicals, beverages',
                'unit_type' => 'volume',
                'default_unit' => 'liters',
                'allowed_units' => json_encode(['liters', 'ml', 'gallons', 'quarts', 'pints']),
                'properties_schema' => json_encode([
                    'viscosity' => 'decimal',
                    'ph_level' => 'decimal',
                    'temperature_range' => 'string',
                    'batch_number' => 'string'
                ]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_types');
    }
};
