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
        Schema::create('inventory_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location');
            $table->integer('capacity')->default(0);
            $table->integer('current_capacity')->default(0);
            $table->string('manager')->nullable();
            $table->string('status')->default('active'); // active, inactive, maintenance
            $table->json('zones')->nullable(); // Store warehouse zones as JSON
            $table->json('metadata')->nullable(); // Store additional warehouse data
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status', 'name']);
            $table->index(['created_by', 'created_at']);

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_warehouses');
    }
};
