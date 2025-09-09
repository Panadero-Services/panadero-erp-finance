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
        Schema::create('ai_training_data', function (Blueprint $table) {
            $table->id();
            $table->text('user_query');
            $table->text('ai_response');
            $table->string('model_used', 50);
            $table->string('module', 50);
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->text('user_feedback')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('was_helpful')->nullable();
            $table->text('suggested_improvement')->nullable();
            $table->string('query_classification', 50)->nullable();
            $table->boolean('training_approved')->default(false);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['module', 'training_approved']);
            $table->index(['query_classification', 'was_helpful']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_training_data');
    }
};
