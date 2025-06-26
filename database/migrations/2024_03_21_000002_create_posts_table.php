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
        // Ensure post_types table exists first
        if (!Schema::hasTable('post_types')) {
            Schema::create('post_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->integer('text_length');
                $table->timestamps();
            });
        }

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->json('options')->nullable();
            $table->json('links')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('post_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug', 128);
            $table->unique(['slug', 'user_id']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_self')->default(false);
            $table->boolean('is_smart')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_types');
    }
}; 