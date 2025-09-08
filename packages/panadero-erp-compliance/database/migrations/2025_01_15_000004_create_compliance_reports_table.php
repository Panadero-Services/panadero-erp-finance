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
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique()->index();
            $table->string('title');
            $table->string('type'); // regulatory, internal, audit, risk, policy
            $table->string('category'); // monthly, quarterly, annual, ad_hoc
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, generated, approved, published
            $table->date('report_date')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('data')->nullable(); // Report data
            $table->json('metrics')->nullable(); // Key metrics
            $table->json('metadata')->nullable(); // Store report-specific data
            $table->string('file_path')->nullable(); // Path to generated report file
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['type', 'category']);
            $table->index(['status', 'report_date']);
            $table->index(['created_by', 'created_at']);
            $table->index(['period_start', 'period_end']);

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
        Schema::dropIfExists('compliance_reports');
    }
};
