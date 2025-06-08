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
        Schema::create('web3_records', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('title',80)->unique();
        $table->integer('web3type_id');
        $table->integer('web3project_id');
        $table->integer('web3chain_id');
        $table->string('category',80);
        $table->string('address',80);
        $table->string('url',128);
        $table->text('abi');
        $table->text('description');
        $table->text('json');
        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        $table->boolean('completed')->default(false);
        $table->boolean('archived')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web3records');
    }
};
