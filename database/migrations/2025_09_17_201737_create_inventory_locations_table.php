<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->index(); // warehouse, zone, shelf, bin
            $table->unsignedBigInteger('parent_id')->nullable();
            
            // Location hierarchy
            $table->string('full_path')->nullable(); // e.g., "Main Warehouse > Zone A > Shelf 1"
            $table->integer('level')->default(0); // 0=warehouse, 1=zone, 2=shelf, 3=bin
            $table->integer('sort_order')->default(0);
            
            // Capacity and constraints
            $table->decimal('capacity_units', 15, 4)->nullable(); // for items
            $table->decimal('capacity_kg', 15, 4)->nullable(); // for bulk materials
            $table->decimal('capacity_liters', 15, 4)->nullable(); // for liquids
            $table->decimal('current_units', 15, 4)->default(0);
            $table->decimal('current_kg', 15, 4)->default(0);
            $table->decimal('current_liters', 15, 4)->default(0);
            
            // Environmental conditions
            $table->boolean('temperature_controlled')->default(false);
            $table->decimal('min_temperature', 5, 2)->nullable(); // °C
            $table->decimal('max_temperature', 5, 2)->nullable(); // °C
            $table->boolean('humidity_controlled')->default(false);
            $table->decimal('min_humidity', 5, 2)->nullable(); // percentage
            $table->decimal('max_humidity', 5, 2)->nullable(); // percentage
            
            // Access and security
            $table->boolean('requires_access_card')->default(false);
            $table->string('access_level')->nullable(); // public, restricted, secure
            $table->boolean('is_active')->default(true);
            
            // Location-specific properties
            $table->json('location_properties')->nullable(); // Additional flexible properties
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'parent_id']);
            $table->index(['level', 'sort_order']);
            $table->index(['is_active', 'type']);
            $table->index(['temperature_controlled', 'is_active']);
            
            // Foreign key for hierarchy
            $table->foreign('parent_id')->references('id')->on('inventory_locations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_locations');
    }
};