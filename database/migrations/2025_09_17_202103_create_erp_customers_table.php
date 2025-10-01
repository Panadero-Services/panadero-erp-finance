<?php
// 2025_09_17_202103_create_erp_customers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('identifier', 8)->unique();
            $table->timestamps();
            $table->json('json')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            
            $table->index(['is_active', 'name']);
            $table->index(['identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_customers');
    }
};