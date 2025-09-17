<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('inventory_products');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses');
            $table->string('zone')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            $table->decimal('current_stock', 15, 4)->default(0);
            $table->decimal('reserved_stock', 15, 4)->default(0);
            $table->decimal('available_stock', 15, 4)->default(0);
            $table->decimal('min_stock_level', 15, 4)->default(0);
            $table->decimal('max_stock_level', 15, 4)->default(0);
            $table->decimal('reorder_point', 15, 4)->default(0);
            $table->decimal('reorder_quantity', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->timestamp('last_counted')->nullable();
            $table->timestamp('last_movement')->nullable();
            $table->string('status')->default('active'); // active, low_stock, out_of_stock, discontinued
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'warehouse_id']);
            $table->index(['status', 'current_stock']);
            $table->index(['warehouse_id', 'zone']);
            $table->index(['last_counted', 'last_movement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_levels');
    }
};