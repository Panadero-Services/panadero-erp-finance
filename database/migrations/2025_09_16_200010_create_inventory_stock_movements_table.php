<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('inventory_stock_items');
            $table->string('movement_type'); // 'in', 'out', 'transfer', 'adjustment'
            $table->decimal('quantity', 15, 4);
            $table->string('unit');
            $table->string('reason'); // 'purchase', 'sale', 'transfer', 'adjustment', 'waste'
            $table->string('reference')->nullable(); // PO number, SO number, etc.
            $table->text('notes')->nullable();
            
            // Location tracking
            $table->foreignId('from_warehouse_id')->nullable()->constrained('inventory_warehouses');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('inventory_warehouses');
            $table->string('from_zone')->nullable();
            $table->string('to_zone')->nullable();
            
            // Batch/Serial tracking
            $table->string('batch_number')->nullable();
            $table->json('serial_numbers')->nullable(); // For items with serial numbers
            $table->date('expiry_date')->nullable(); // For items with expiry dates
            
            // Cost tracking
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 4)->nullable();
            
            // Audit
            $table->unsignedBigInteger('created_by');
            $table->timestamp('movement_date');
            $table->timestamps();

            // Indexes
            $table->index(['stock_item_id', 'movement_date']);
            $table->index(['movement_type', 'movement_date']);
            $table->index(['reference', 'movement_type']);
            $table->index(['batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_movements');
    }
};
