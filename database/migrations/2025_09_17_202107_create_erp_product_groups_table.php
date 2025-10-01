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
        Schema::create('erp_product_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('code', 16)->unique(); // optional short identifier
            $table->foreignId('erp_product_type_id')
                  ->constrained('erp_product_types')
                  ->onDelete('restrict'); // product groups must belong to a type
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('erp_product_groups')
                  ->onDelete('cascade'); // deleting parent deletes children
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['erp_product_type_id', 'is_active'], 'idx_group_type_active');
            $table->index(['parent_id', 'is_active'], 'idx_group_parent_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop product groups table
        Schema::dropIfExists('erp_product_groups');
    }
};
