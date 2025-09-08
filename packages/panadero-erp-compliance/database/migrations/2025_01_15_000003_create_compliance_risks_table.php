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
        Schema::create('compliance_risks', function (Blueprint $table) {
            $table->id();
            $table->string('risk_id')->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // regulatory, operational, financial, reputational
            $table->string('type'); // compliance, security, data, process
            $table->string('status')->default('identified'); // identified, assessed, mitigated, accepted, closed
            $table->string('probability')->default('medium'); // low, medium, high, very_high
            $table->string('impact')->default('medium'); // low, medium, high, critical
            $table->string('risk_level')->default('medium'); // low, medium, high, critical
            $table->text('mitigation_plan')->nullable();
            $table->text('controls')->nullable();
            $table->date('identified_date')->nullable();
            $table->date('target_resolution_date')->nullable();
            $table->date('actual_resolution_date')->nullable();
            $table->json('metadata')->nullable(); // Store risk-specific data
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['category', 'type']);
            $table->index(['status', 'risk_level']);
            $table->index(['owner_id', 'created_at']);
            $table->index(['probability', 'impact']);

            // Foreign keys
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_risks');
    }
};
