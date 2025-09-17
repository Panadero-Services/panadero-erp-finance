<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_closeouts', function (Blueprint $table) {
            $table->id();
            $table->string('period_type'); // daily, weekly, monthly, quarterly, yearly
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses');
            $table->string('status')->default('pending'); // pending, in_progress, completed, approved
            $table->integer('total_products')->default(0);
            $table->decimal('total_value_start', 15, 4)->default(0);
            $table->decimal('total_value_end', 15, 4)->default(0);
            $table->decimal('total_movements_in', 15, 4)->default(0);
            $table->decimal('total_movements_out', 15, 4)->default(0);
            $table->decimal('total_adjustments', 15, 4)->default(0);
            $table->decimal('variance_amount', 15, 4)->default(0);
            $table->decimal('variance_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['period_type', 'period_start', 'period_end']);
            $table->index(['warehouse_id', 'status']);
            $table->index(['created_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_closeouts');
    }
};