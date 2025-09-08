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
        Schema::create('compliance_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_number')->unique()->index();
            $table->string('title');
            $table->string('type'); // internal, external, regulatory, security
            $table->string('scope'); // full, partial, specific
            $table->text('description')->nullable();
            $table->string('status')->default('planned'); // planned, in_progress, completed, cancelled
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('due_date')->nullable();
            $table->json('findings')->nullable(); // Audit findings
            $table->json('recommendations')->nullable(); // Recommendations
            $table->string('risk_level')->default('medium'); // low, medium, high, critical
            $table->json('metadata')->nullable(); // Store audit-specific data
            $table->unsignedBigInteger('auditor_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['type', 'status']);
            $table->index(['status', 'due_date']);
            $table->index(['auditor_id', 'created_at']);
            $table->index(['risk_level', 'status']);

            // Foreign keys
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_audits');
    }
};
