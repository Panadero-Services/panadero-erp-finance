<?php
// 2025_09_17_202201_create_erp_units_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('erp_units')) {
            Schema::create('erp_units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('symbol')->nullable();
                $table->string('type')->default('unit'); // unit, weight, volume, length
                $table->decimal('conversion_factor', 10, 4)->default(1);
                $table->string('base_unit')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['type', 'is_active']);
                $table->index(['symbol', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_units');
    }
};