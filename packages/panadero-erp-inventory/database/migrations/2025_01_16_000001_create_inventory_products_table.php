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
            $table->string('group')->index();
            $table->enum('type', ['items', 'bulk', 'liquids']);
            $table->string('unit')->default('units');
            $table->json('specifications')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['group', 'category']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
