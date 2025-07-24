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
        Schema::create('world_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_world_id')->constrained()->onDelete('cascade');
            $table->string('event_type', 100); // resource_spawn, player_joined, battle_started, etc.
            $table->string('event_name', 255);
            $table->json('event_data')->nullable();
            $table->timestamp('event_time')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['game_world_id', 'event_type']);
            $table->index(['event_time', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('world_events');
    }
}; 