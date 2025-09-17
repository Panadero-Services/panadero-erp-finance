<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_incoming_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('inventory_incoming_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('inventory_products');
            $table->decimal('quantity_ordered', 15, 4);
            $table->decimal('quantity_received', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('total_price', 15, 4);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'product_id']);
            $table->index(['product_id', 'quantity_ordered']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_incoming_order_items');
    }
};