<?php
// 2025_09_17_202115_create_erp_orders_in_lines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('erp_order_in_lines')) {
            Schema::create('erp_order_in_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_in_id')->constrained('erp_orders_in')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('erp_products')->onDelete('cascade');
                $table->decimal('qty', 15, 4)->default(0);
                $table->foreignId('site_id')->constrained('erp_sites')->onDelete('restrict');
                $table->foreignId('status_id')->constrained('erp_status')->onDelete('restrict');
                $table->json('eventHandlers')->nullable();
                $table->json('stockHandler')->nullable();
                $table->unsignedBigInteger('sample_id')->nullable(); // Just a regular column, no foreign key
                $table->timestamps();
                $table->json('json')->nullable();
                $table->text('comment')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_locked')->default(false);
                
                $table->index(['order_in_id', 'is_active']);
                $table->index(['product_id', 'status_id']);
                $table->index(['site_id', 'status_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_order_in_lines');
    }
};