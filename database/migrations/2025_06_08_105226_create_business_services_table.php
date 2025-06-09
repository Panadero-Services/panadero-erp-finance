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
        Schema::create('business_services', function (Blueprint $table) {
            $table->id();
            $table->string('item', 8)->unique();
            $table->string('title', 32);
            $table->text('description');
            $table->string('color', 32);
            $table->string('version', 32);
            $table->string('icon', 32);
            $table->json('options')->nullable();
            $table->json('links')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('path', 16);
            $table->string('status', 16);
            $table->integer('progress');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_services');
    }
}; 