<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the main vendors table - a shared entity provided by the framework API
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code', 50)->unique()->index(); // Unique vendor identifier
            $table->string('name'); // Vendor business name
            $table->string('legal_name')->nullable(); // Legal registered business name
            $table->string('contact_person')->nullable(); // Primary contact person
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            
            // Address fields
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->nullable(); // ISO country code
            
            // Business information
            $table->string('tax_id')->nullable(); // Tax identification number
            $table->string('registration_number')->nullable(); // Business registration number
            $table->enum('vendor_type', ['supplier', 'service_provider', 'contractor', 'consultant', 'other'])->default('supplier');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
            
            // System fields
            $table->json('metadata')->nullable(); // Store additional vendor-specific data
            $table->string('external_id')->nullable(); // For API integrations
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['vendor_type', 'status']);
            $table->index(['country', 'is_active']);
            $table->index(['is_approved', 'status']);
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};

