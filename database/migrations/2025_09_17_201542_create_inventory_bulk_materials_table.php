<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_bulk_materials', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->string('subcategory')->nullable()->index();
            $table->string('brand')->nullable();
            
            // Bulk-specific properties
            $table->decimal('density', 8, 4)->nullable(); // kg/m³
            $table->decimal('moisture_content', 5, 2)->nullable(); // percentage
            $table->decimal('purity', 5, 2)->nullable(); // percentage
            $table->string('particle_size')->nullable(); // fine, medium, coarse
            $table->string('storage_requirements')->nullable(); // dry, cool, sealed
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Pricing (per kg)
            $table->decimal('cost_per_kg', 15, 4)->default(0);
            $table->decimal('price_per_kg', 15, 4)->default(0);
            $table->decimal('cost_per_ton', 15, 4)->nullable();
            $table->decimal('price_per_ton', 15, 4)->nullable();
            
            // Business rules (in kg)
            $table->decimal('min_stock_kg', 15, 4)->default(0);
            $table->decimal('max_stock_kg', 15, 4)->default(0);
            $table->decimal('reorder_point_kg', 15, 4)->default(0);
            $table->decimal('reorder_quantity_kg', 15, 4)->default(0);
            
            // Status and tracking
            $table->string('status')->default('active'); // active, inactive, discontinued
            $table->boolean('requires_batch_tracking')->default(true);
            $table->boolean('is_hazardous')->default(false);
            $table->boolean('is_perishable')->default(false);
            $table->json('bulk_properties')->nullable(); // Additional flexible properties
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category', 'subcategory']);
            $table->index(['status', 'created_at']);
            $table->index(['brand', 'category']);
            $table->index(['requires_batch_tracking', 'status']);
            $table->index(['expiry_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_bulk_materials');
    }
};