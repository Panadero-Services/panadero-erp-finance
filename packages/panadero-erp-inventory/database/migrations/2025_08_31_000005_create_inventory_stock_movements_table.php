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
            $table->unsignedBigInteger('item_id');
            $table->string('sku'); // Denormalized for performance
            $table->enum('movement_type', ['in', 'out', 'adjustment']); // in, out, adjustment
            $table->integer('quantity');
            $table->string('reason'); // Purchase Order, Sales Order, Adjustment, etc.
            $table->string('reference')->nullable(); // PO number, SO number, etc.
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Store additional movement data
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['item_id', 'created_at']);
            $table->index(['movement_type', 'created_at']);
            $table->index(['reference', 'created_at']);
            $table->index(['created_by', 'created_at']);

            // Foreign keys
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
