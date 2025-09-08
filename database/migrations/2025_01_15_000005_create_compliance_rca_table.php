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
        Schema::create('compliance_rca', function (Blueprint $table) {
            $table->id();
            $table->string('rca_id')->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('incident_type'); // compliance_violation, audit_finding, risk_event, policy_breach
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('open'); // open, in_progress, completed, closed
            $table->text('problem_statement')->nullable();
            $table->text('immediate_actions')->nullable();
            $table->text('root_causes')->nullable(); // JSON array of root causes
            $table->text('contributing_factors')->nullable(); // JSON array of contributing factors
            $table->text('corrective_actions')->nullable(); // JSON array of corrective actions
            $table->text('preventive_measures')->nullable(); // JSON array of preventive measures
            $table->date('incident_date')->nullable();
            $table->date('discovery_date')->nullable();
            $table->date('target_resolution_date')->nullable();
            $table->date('actual_resolution_date')->nullable();
            $table->json('metadata')->nullable(); // Store RCA-specific data
            $table->json('ai_analysis')->nullable(); // AI-powered analysis results
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['incident_type', 'severity']);
            $table->index(['status', 'severity']);
            $table->index(['assigned_to', 'created_at']);
            $table->index(['incident_date', 'discovery_date']);

            // Foreign keys
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_rca');
    }
};
