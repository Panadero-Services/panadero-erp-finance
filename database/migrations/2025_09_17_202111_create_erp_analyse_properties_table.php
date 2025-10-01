<?php
// 2025_09_17_202111_create_erp_analyse_properties_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_analyse_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->string('data_type')->default('string'); // string, number, boolean, date
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_analyse_properties');
    }
};