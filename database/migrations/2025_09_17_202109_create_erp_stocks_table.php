<?php
// 2025_09_17_202109_create_erp_stocks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->constrained('erp_storages')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('erp_products')->onDelete('cascade');
            $table->decimal('qty', 15, 4)->default(0);
            $table->timestamps();
            $table->json('json')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            
            $table->unique(['storage_id', 'product_id']);
            $table->index(['product_id', 'is_active']);
            $table->index(['storage_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_stocks');
    }
};