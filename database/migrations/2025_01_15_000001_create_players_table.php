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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('email', 255)->unique()->nullable();
            $table->string('callsign', 50)->unique()->nullable();
            $table->string('avatar', 255)->nullable();
            $table->json('preferences')->nullable(); // Game preferences, settings
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_game_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
            $table->index('callsign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
}; 