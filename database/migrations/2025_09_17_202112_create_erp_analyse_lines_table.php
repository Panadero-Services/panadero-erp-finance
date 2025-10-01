<?php
// 2025_09_17_202112_create_erp_analyse_lines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_analyse_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('erp_analyses')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('erp_analyse_properties')->onDelete('cascade');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->text('comment')->nullable();
            
            $table->index(['analysis_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_analyse_lines');
    }
};