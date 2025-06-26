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
        Schema::create('futures', function (Blueprint $table) {
            $table->id();
            $table->string('item', 80)->unique()->default('');
            $table->string('title', 80)->default('title');
            $table->text('description');
            $table->string('color', 24)->default('blue');
            $table->string('version', 24)->default('v0.0.0');
            $table->string('icon', 80)->default('');
            $table->string('status', 32)->default('idle');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('project_id')->constrained('projects');
            $table->json('options');
            $table->json('links');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);

            $table->index('user_id');
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futures');
    }
}; 