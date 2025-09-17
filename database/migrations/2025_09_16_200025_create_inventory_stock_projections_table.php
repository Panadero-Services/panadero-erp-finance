// Migration: Create stock projections table
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('inventory_stock_items');
            $table->date('projection_date');
            $table->decimal('projected_stock', 15, 4);
            $table->decimal('incoming_quantity', 15, 4)->default(0);
            $table->decimal('outgoing_quantity', 15, 4)->default(0);
            $table->string('status'); // 'normal', 'low', 'critical', 'excess'
            $table->json('incoming_details')->nullable(); // Details of incoming stock
            $table->json('outgoing_details')->nullable(); // Details of outgoing stock
            $table->timestamps();

            // Indexes
            $table->index(['stock_item_id', 'projection_date']);
            $table->index(['projection_date', 'status']);
            $table->unique(['stock_item_id', 'projection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_projections');
    }
};