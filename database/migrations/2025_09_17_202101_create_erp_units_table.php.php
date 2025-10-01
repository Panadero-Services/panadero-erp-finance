<?php
// 2025_09_17_202101_create_erp_units_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);       // e.g. Kilogram, Liter, Piece
            $table->string('symbol', 16);     // e.g. kg, L, pcs
            $table->boolean('is_decimal')->default(true); // true for kg/L, false for pcs
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_units');
    }
};