<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_discrete_items');
            $table->foreignId('location_id')->constrained('inventory_locations');
            
            // Stock levels (in units)
            $table->integer('current_units')->default(0);
            $table->integer('reserved_units')->default(0);
            $table->integer('available_units')->default(0);
            $table->integer('min_units')->default(0);
            $table->integer('max_units')->default(0);
            $table->integer('reorder_point_units')->default(0);
            $table->integer('reorder_quantity_units')->default(0);
            
            // Serial number tracking
            $table->json('serial_numbers')->nullable(); // Array of serial numbers
            $table->integer('serial_count')->default(0);
            
            // Cost tracking
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->decimal('average_cost', 15, 4)->default(0);
            
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
            $table->index(['item_id', 'location_id']);
            $table->index(['status', 'current_units']);
            $table->index(['location_id', 'status']);
            $table->index(['last_counted', 'last_movement']);
            
            // Unique constraint
            $table->unique(['item_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_stock');
    }
};