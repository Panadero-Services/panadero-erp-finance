<?php
// 2025_09_17_202102_create_erp_product_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->unique();  // e.g. normal, bulk, liquid
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_product_types');
    }
};