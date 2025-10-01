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
        Schema::create('erp_products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('identifier', 16)->unique();
            $table->foreignId('erp_product_type_id')->constrained('erp_product_types')->onDelete('restrict');
            $table->foreignId('erp_product_group_id')->nullable()->constrained('erp_product_groups')->onDelete('restrict');
            $table->foreignId('erp_brand_id')->nullable()->constrained('erp_brands')->onDelete('restrict');
            $table->foreignId('unit_id')->constrained('erp_units')->onDelete('restrict');
            $table->json('json')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            
            $table->index(['erp_product_type_id', 'is_active'], 'idx_product_type_active');
            $table->index(['erp_product_group_id', 'is_active'], 'idx_product_group_active');
            $table->index(['erp_brand_id', 'is_active'], 'idx_product_brand_active');
            $table->index('identifier', 'idx_product_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_products');
    }
};