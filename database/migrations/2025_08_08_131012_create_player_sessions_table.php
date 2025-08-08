<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('player_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('player_id'); // Using string since it might be a name/callsign
            $table->string('world_id');  // Using string to match game_worlds server_id
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('disconnected_at')->nullable();
            $table->json('metadata')->nullable(); // For any additional session data
            $table->timestamps();

            // Add indexes
            $table->index('player_id');
            $table->index('world_id');
            $table->index('status');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_sessions');
    }
};