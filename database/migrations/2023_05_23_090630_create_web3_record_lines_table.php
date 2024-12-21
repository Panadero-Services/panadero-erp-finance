<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web3_record_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('web3record_id');
            $table->string('line',80);
            $table->integer('line_nr');
            $table->text('abi');
            $table->text('parameters');
            $table->text('value');
            $table->integer('int_value');
            $table->timestamps();
            $table->string('columns',40);
            $table->string('style',80);
            $table->string('type',40);
            $table->string('detail',120);
            $table->boolean('is_active');
            $table->boolean('is_live');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web3_record_lines');
    }
};
