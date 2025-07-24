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
        Schema::create('game_worlds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('server_url', 255);
            $table->string('server_id', 50)->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->integer('max_players')->default(100);
            $table->integer('current_players')->default(0);
            $table->json('world_config')->nullable(); // World-specific settings
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'current_players']);
            $table->index('server_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_worlds');
    }
}; 