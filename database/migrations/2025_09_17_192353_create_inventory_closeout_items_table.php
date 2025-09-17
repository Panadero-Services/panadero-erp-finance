<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_closeout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('closeout_id')->constrained('inventory_closeouts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('inventory_products');
            $table->decimal('stock_start', 15, 4)->default(0);
            $table->decimal('stock_end', 15, 4)->default(0);
            $table->decimal('movements_in', 15, 4)->default(0);
            $table->decimal('movements_out', 15, 4)->default(0);
            $table->decimal('adjustments', 15, 4)->default(0);
            $table->decimal('value_start', 15, 4)->default(0);
            $table->decimal('value_end', 15, 4)->default(0);
            $table->decimal('unit_cost_avg', 15, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['closeout_id', 'product_id']);
            $table->index(['product_id', 'stock_start', 'stock_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_closeout_items');
    }
};