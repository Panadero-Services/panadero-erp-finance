<?php
// 2025_09_17_202107_create_erp_storages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_storages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // warehouse, zone, shelf, bin
            $table->foreignId('site_id')->constrained('erp_sites')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('erp_units')->onDelete('restrict');
            $table->decimal('max_units', 15, 4)->default(0);
            $table->timestamps();
            $table->json('json')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            
            $table->index(['site_id', 'type']);
            $table->index(['is_active', 'type']);
            $table->index(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_storages');
    }
};