<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->json('provider');
            $table->json('processor');
            $table->json('executor');
            $table->string('status')->default('stopped');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
            $table->boolean('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
