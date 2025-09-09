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
        Schema::create('a_i_error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('error_type', 100);
            $table->json('error_data');
            $table->string('model', 50)->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('is_timeout')->default(false);
            $table->timestamps();
            
            $table->index(['error_type', 'created_at']);
            $table->index('is_timeout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_error_logs');
    }
};
