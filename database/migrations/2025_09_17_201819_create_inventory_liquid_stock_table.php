<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_liquid_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liquid_id')->constrained('inventory_liquids');
            $table->foreignId('location_id')->constrained('inventory_locations');
            
            // Stock levels (in liters)
            $table->decimal('current_liters', 15, 4)->default(0);
            $table->decimal('reserved_liters', 15, 4)->default(0);
            $table->decimal('available_liters', 15, 4)->default(0);
            $table->decimal('min_liters', 15, 4)->default(0);
            $table->decimal('max_liters', 15, 4)->default(0);
            $table->decimal('reorder_point_liters', 15, 4)->default(0);
            $table->decimal('reorder_quantity_liters', 15, 4)->default(0);
            
            // Lot tracking
            $table->json('lot_numbers')->nullable(); // Array of lot numbers
            $table->integer('lot_count')->default(0);
            $table->date('oldest_lot_date')->nullable();
            $table->date('newest_lot_date')->nullable();
            
            // Cost tracking
            $table->decimal('cost_per_liter', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->decimal('average_cost_per_liter', 15, 4)->default(0);
            
            // Quality tracking
            $table->decimal('average_viscosity', 8, 4)->nullable();
            $table->decimal('average_ph', 4, 2)->nullable();
            $table->string('quality_status')->default('good'); // good, degraded, expired
            $table->boolean('temperature_monitored')->default(false);
            $table->decimal('current_temperature', 5, 2)->nullable(); // °C
            
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
            $table->index(['liquid_id', 'location_id']);
            $table->index(['status', 'current_liters']);
            $table->index(['location_id', 'status']);
            $table->index(['last_counted', 'last_movement']);
            $table->index(['quality_status', 'oldest_lot_date']);
            $table->index(['temperature_monitored', 'current_temperature'], 'temp_monitored_idx');
            
            // Unique constraint
            $table->unique(['liquid_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_liquid_stock');
    }
};