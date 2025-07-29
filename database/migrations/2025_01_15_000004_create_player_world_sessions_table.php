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
        Schema::create('player_world_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id');
            $table->foreignId('game_world_id');
            $table->string('session_id');
            $table->string('player_name');
            $table->timestamp('connected_at')->nullable();
            $table->json('player_state')->nullable();    // Current player state
            $table->json('resources')->nullable();       // Player resources
            $table->string('status');        // active, disconnected, transferring
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active')->nullable()->useCurrent();  // Fixed: Added nullable and default
            $table->timestamp('disconnected_at')->nullable();
            $table->json('recovery_data')->nullable();   // Data needed for recovery
            $table->string('fallback_server_id')->nullable();
            $table->integer('recovery_attempts')->default(0);
            $table->string('recovery_stage')->nullable(); // For tracking multi-stage recovery
            $table->timestamps();
            
            $table->index(['player_id', 'is_active']);
            $table->index(['game_world_id', 'is_active']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_world_sessions');
    }
}; 