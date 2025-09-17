// Migration: Create normalized stock items table
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->string('subcategory')->nullable()->index();
            
            // Stock type relationship
            $table->foreignId('stock_type_id')->constrained('inventory_stock_types');
            
            // Basic stock info
            $table->decimal('current_stock', 15, 4)->default(0);
            $table->decimal('min_stock', 15, 4)->default(0);
            $table->decimal('max_stock', 15, 4)->default(0);
            $table->string('unit')->default('units');
            
            // Pricing
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 4)->default(0);
            
            // Location
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses');
            $table->string('zone')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            
            // Status and tracking
            $table->string('status')->default('active'); // active, inactive, discontinued
            $table->json('type_specific_properties')->nullable(); // Store type-specific data
            $table->json('metadata')->nullable(); // Additional flexible data
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['stock_type_id', 'category']);
            $table->index(['current_stock', 'min_stock']);
            $table->index(['warehouse_id', 'zone']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_items');
    }
};