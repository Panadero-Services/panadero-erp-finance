<?php
// 2025_09_17_202114_create_erp_orders_out_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_orders_out', function (Blueprint $table) {
            $table->id();
            $table->string('ordernr', 32)->unique();
            $table->foreignId('customer_id')->constrained('erp_customers')->onDelete('restrict');
            $table->foreignId('site_id')->constrained('erp_sites')->onDelete('restrict');
            $table->foreignId('status_id')->constrained('erp_status')->onDelete('restrict');
            $table->timestamps();
            $table->json('eventHandlers')->nullable();
            $table->json('json')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            
            $table->index(['customer_id', 'status_id']);
            $table->index(['site_id', 'status_id']);
            $table->index(['ordernr', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_orders_out');
    }
};