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
        Schema::create('a_i_success_logs', function (Blueprint $table) {
            $table->id();
            $table->json('success_data');
            $table->string('model', 50)->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->integer('tokens_generated')->nullable();
            $table->timestamps();
            
            $table->index(['model', 'created_at']);
            $table->index('response_time_ms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_success_logs');
    }
};
