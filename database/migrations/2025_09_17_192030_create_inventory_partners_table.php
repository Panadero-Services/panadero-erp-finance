<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // supplier, customer, forwarder
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('address')->nullable(); // street, city, state, zip, country
            $table->string('tax_id')->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('credit_limit', 15, 4)->default(0);
            $table->decimal('rating', 3, 2)->default(0); // 0.00 to 5.00
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['name', 'type']);
            $table->index(['rating', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_partners');
    }
};