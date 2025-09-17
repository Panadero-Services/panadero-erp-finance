<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_incoming_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('supplier_id')->constrained('inventory_suppliers');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses');
            $table->date('order_date');
            $table->date('expected_delivery')->nullable();
            $table->date('actual_delivery')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, in_transit, delivered, completed, cancelled
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['supplier_id', 'order_date']);
            $table->index(['status', 'priority']);
            $table->index(['warehouse_id', 'expected_delivery']);
            $table->index(['created_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_incoming_orders');
    }
};