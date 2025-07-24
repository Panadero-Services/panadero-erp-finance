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
        Schema::create('player_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->string('resource_type', 50); // gold, water, kryptonite, etc.
            $table->integer('amount')->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            $table->unique(['player_id', 'resource_type']);
            $table->index(['player_id', 'resource_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_resources');
    }
}; 