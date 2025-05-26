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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action',128);
            $table->integer('user_id')->nullable();
            $table->string('module',128);
            $table->string('node',128);
            $table->string('team',128)->nullable();
            $table->string('project',128)->nullable();
            $table->mediumText('content')->nullable();
            $table->mediumText('json')->nullable();
            $table->mediumText('tags')->nullable();
            $table->timestamp('created_at')->useCurrent(); 
            $table->timestamp('updated_at')->useCurrent(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
