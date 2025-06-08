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
        // Create post_types table if it doesn't exist
        if (!Schema::hasTable('post_types')) {
            Schema::create('post_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->integer('text_length');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_types');
    }
}; 