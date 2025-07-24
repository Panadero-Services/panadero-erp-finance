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
        Schema::create('world_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_world_id')->nullable()->constrained('game_worlds')->onDelete('set null');
            $table->foreignId('to_world_id')->constrained('game_worlds')->onDelete('cascade');
            $table->string('transfer_type', 50); // warp, teleport, emergency
            $table->json('resources_transferred')->nullable(); // Resources carried over
            $table->json('player_state')->nullable(); // Player state at transfer
            $table->timestamp('transfer_time')->useCurrent();
            $table->boolean('successful')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['player_id', 'transfer_time']);
            $table->index(['from_world_id', 'to_world_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('world_transfers');
    }
}; 