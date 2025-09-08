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
        Schema::create('compliance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // regulatory, internal, industry, security
            $table->string('category'); // gdpr, sox, iso, pci, hipaa, custom
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('draft'); // draft, active, inactive, archived
            $table->string('version')->default('1.0');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('metadata')->nullable(); // Store policy-specific data
            $table->json('requirements')->nullable(); // Compliance requirements
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['type', 'category']);
            $table->index(['status', 'effective_date']);
            $table->index(['created_by', 'created_at']);
            $table->index(['version', 'status']);

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_policies');
    }
};
