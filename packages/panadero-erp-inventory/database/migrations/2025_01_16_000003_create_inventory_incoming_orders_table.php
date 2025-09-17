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
            $table->string('order_number')->unique()->index();
            $table->string('supplier');
            $table->date('order_date');
            $table->date('expected_date');
            $table->enum('status', ['planned', 'unloading', 'completed'])->default('planned');
            $table->decimal('total_value', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index(['status', 'expected_date']);
            $table->index(['supplier', 'order_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_incoming_orders');
    }
};
