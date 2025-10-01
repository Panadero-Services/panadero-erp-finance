<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_bulk_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('inventory_bulk_materials');
            $table->foreignId('location_id')->constrained('inventory_locations');
            
            // Stock levels (in kg)
            $table->decimal('current_kg', 15, 4)->default(0);
            $table->decimal('reserved_kg', 15, 4)->default(0);
            $table->decimal('available_kg', 15, 4)->default(0);
            $table->decimal('min_kg', 15, 4)->default(0);
            $table->decimal('max_kg', 15, 4)->default(0);
            $table->decimal('reorder_point_kg', 15, 4)->default(0);
            $table->decimal('reorder_quantity_kg', 15, 4)->default(0);
            
            // Batch tracking
            $table->json('batch_numbers')->nullable(); // Array of batch numbers
            $table->integer('batch_count')->default(0);
            $table->date('oldest_batch_date')->nullable();
            $table->date('newest_batch_date')->nullable();
            
            // Cost tracking
            $table->decimal('cost_per_kg', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->decimal('average_cost_per_kg', 15, 4)->default(0);
            
            // Quality tracking
            $table->decimal('average_moisture', 5, 2)->nullable();
            $table->decimal('average_purity', 5, 2)->nullable();
            $table->string('quality_status')->default('good'); // good, degraded, expired
            
            // Status and tracking
            $table->string('status')->default('active'); // active, low_stock, out_of_stock, discontinued
            $table->timestamp('last_counted')->nullable();
            $table->timestamp('last_movement')->nullable();
            $table->text('notes')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['material_id', 'location_id']);
            $table->index(['status', 'current_kg']);
            $table->index(['location_id', 'status']);
            $table->index(['last_counted', 'last_movement']);
            $table->index(['quality_status', 'oldest_batch_date']);
            
            // Unique constraint
            $table->unique(['material_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_bulk_stock');
    }
};