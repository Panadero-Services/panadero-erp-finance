<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_liquids', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->string('subcategory')->nullable()->index();
            $table->string('brand')->nullable();
            
            // Liquid-specific properties
            $table->decimal('viscosity', 8, 4)->nullable(); // cP (centipoise)
            $table->decimal('ph_level', 4, 2)->nullable(); // pH scale
            $table->string('temperature_range')->nullable(); // e.g., "2-8°C"
            $table->string('container_type')->nullable(); // bottle, drum, tank
            $table->decimal('container_size_liters', 8, 3)->nullable();
            $table->string('handling_requirements')->nullable(); // refrigerated, sealed, etc.
            $table->string('lot_number')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Pricing (per liter)
            $table->decimal('cost_per_liter', 15, 4)->default(0);
            $table->decimal('price_per_liter', 15, 4)->default(0);
            $table->decimal('cost_per_gallon', 15, 4)->nullable();
            $table->decimal('price_per_gallon', 15, 4)->nullable();
            
            // Business rules (in liters)
            $table->decimal('min_stock_liters', 15, 4)->default(0);
            $table->decimal('max_stock_liters', 15, 4)->default(0);
            $table->decimal('reorder_point_liters', 15, 4)->default(0);
            $table->decimal('reorder_quantity_liters', 15, 4)->default(0);
            
            // Status and tracking
            $table->string('status')->default('active'); // active, inactive, discontinued
            $table->boolean('requires_lot_tracking')->default(true);
            $table->boolean('is_hazardous')->default(false);
            $table->boolean('is_perishable')->default(false);
            $table->boolean('requires_refrigeration')->default(false);
            $table->json('liquid_properties')->nullable(); // Additional flexible properties
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category', 'subcategory']);
            $table->index(['status', 'created_at']);
            $table->index(['brand', 'category']);
            $table->index(['requires_lot_tracking', 'status']);
            $table->index(['expiry_date', 'status']);
            $table->index(['requires_refrigeration', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_liquids');
    }
};