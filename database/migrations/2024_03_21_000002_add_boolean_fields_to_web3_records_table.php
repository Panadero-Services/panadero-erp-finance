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
        Schema::table('web3_records', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('json');
            $table->boolean('is_live')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web3_records', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_live']);
        });
    }
}; 