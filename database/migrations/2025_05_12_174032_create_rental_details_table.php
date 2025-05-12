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
        Schema::create('rental_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->text('json');
            $table->decimal('rental_price_per_day', 10, 2)->unsigned();
            $table->integer('available_quantity')->unsigned()->default(0);
            $table->integer('min_rental_days')->default(1);
            $table->integer('max_rental_days')->default(30);
            $table->timestamps();
            $table->boolean('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_details');
    }
};
