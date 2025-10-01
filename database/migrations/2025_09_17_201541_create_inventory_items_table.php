<?php
// Modified: 2025_09_17_201541_create_inventory_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();
                $table->string('sku')->unique()->index();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('category')->index();
                $table->string('subcategory')->nullable()->index();
                $table->string('brand')->nullable();
                
                // Item-specific properties
                $table->decimal('weight', 8, 3)->nullable(); // in grams
                $table->json('dimensions')->nullable(); // length, width, height in cm
                $table->string('serial_number')->nullable()->unique();
                $table->string('barcode')->nullable()->unique();
                $table->string('packaging_type')->nullable(); // box, bag, individual
                $table->integer('units_per_package')->default(1);
                
                // Pricing (per unit)
                $table->decimal('unit_cost', 15, 4)->default(0);
                $table->decimal('unit_price', 15, 4)->default(0);
                $table->decimal('package_cost', 15, 4)->nullable();
                $table->decimal('package_price', 15, 4)->nullable();
                
                // Business rules
                $table->integer('min_stock_units')->default(0);
                $table->integer('max_stock_units')->default(0);
                $table->integer('reorder_point_units')->default(0);
                $table->integer('reorder_quantity_units')->default(0);
                
                // Status and tracking
                $table->string('status')->default('active'); // active, inactive, discontinued
                $table->boolean('requires_serial')->default(false);
                $table->boolean('is_fragile')->default(false);
                $table->boolean('is_hazardous')->default(false);
                $table->json('item_properties')->nullable(); // Additional flexible properties
                
                // Audit fields
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index(['category', 'subcategory']);
                $table->index(['status', 'created_at']);
                $table->index(['brand', 'category']);
                $table->index(['requires_serial', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};