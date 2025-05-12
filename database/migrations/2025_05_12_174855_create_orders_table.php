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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->text('json');
            //$table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->enum('order_type', ['sale', 'rental']); // or boolean is_rental
            $table->date('order_date')->default(now());
            $table->timestamps();
            $table->boolean('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
