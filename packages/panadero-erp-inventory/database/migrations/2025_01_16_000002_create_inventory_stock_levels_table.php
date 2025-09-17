<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('inventory_products');
            $table->string('sku')->index();
            $table->string('name');
            $table->decimal('current_stock', 15, 4)->default(0);
            $table->decimal('min_stock', 15, 4)->default(0);
            $table->decimal('max_stock', 15, 4)->default(0);
            $table->string('unit')->default('units');
            $table->string('location')->nullable();
            $table->string('zone')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            $table->enum('status', ['normal', 'low', 'out'])->default('normal');
            $table->timestamp('last_updated')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'current_stock']);
            $table->index(['location', 'zone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_levels');
    }
};
