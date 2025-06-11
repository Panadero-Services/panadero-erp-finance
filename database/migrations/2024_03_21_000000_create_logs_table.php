<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 128);
            $table->string('module', 128);
            $table->string('node', 128);
            $table->string('team', 128)->nullable();
            $table->string('project', 128)->nullable();
            $table->text('content')->nullable();
            $table->text('json')->nullable();
            $table->string('tags')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}; 