<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->string('subcategory')->nullable()->index();
            $table->string('brand')->nullable();
            $table->string('unit')->default('units');
            $table->decimal('weight', 8, 3)->nullable();
            $table->json('dimensions')->nullable(); // length, width, height
            $table->boolean('is_active')->default(true);
            $table->boolean('is_digital')->default(false);
            $table->boolean('requires_serial')->default(false);
            $table->boolean('is_bulk')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['category', 'subcategory']);
            $table->index(['is_active', 'created_at']);
            $table->index(['brand', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};