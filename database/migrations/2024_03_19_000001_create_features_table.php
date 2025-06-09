<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('item')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('color')->nullable();
            $table->string('version')->nullable();
            $table->string('icon')->nullable();
            $table->json('options')->nullable();
            $table->json('links')->nullable();
            $table->string('path')->nullable();
            $table->string('status')->default('featured');
            $table->integer('progress')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('features');
    }
}; 