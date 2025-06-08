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
        Schema::create('sections', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('title',128);
        $table->foreignId('page_id')->index();
        $table->string('file',128);
        $table->text('subtitle');
        $table->string('icon',128);
        $table->string('image',128);
        $table->text('slogan');
        $table->text('html');
        $table->text('css');
        $table->text('features');
        $table->text('settings');
        $table->text('self');
        $table->text('self_admin');
        $table->integer('priority');
        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        $table->boolean('is_active');
        $table->boolean('self_auth');
        $table->boolean('animate')->default(false);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
