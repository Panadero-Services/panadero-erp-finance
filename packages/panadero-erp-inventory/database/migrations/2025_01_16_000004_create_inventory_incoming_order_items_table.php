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
            $table->foreignId('order_id')->constrained('inventory_incoming_orders');
            $table->string('sku');
            $table->string('name');
            $table->decimal('quantity', 15, 4);
            $table->string('unit');
            $table->decimal('received', 15, 4)->default(0);
            $table->timestamps();
            
            $table->index(['order_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_incoming_order_items');
    }
};
