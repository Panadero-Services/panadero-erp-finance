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
        Schema::create('finance_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->index();
            $table->string('section', 10)->index(); // AP, AR, GL, CF, TX, FA, BD, AU
            $table->string('type'); // payable, receivable, journal, cashflow, tax, asset, budget, audit
            $table->string('category'); // expense, revenue, adjustment, cash, tax, asset, budget, audit
            $table->string('description')->nullable();
            $table->string('status')->default('draft'); // draft, pending, approved, completed, cancelled
            $table->decimal('amount', 15, 2)->default(0);
            $table->json('metadata')->nullable(); // Store section-specific data
            $table->string('reference_id')->nullable(); // Link to original record
            $table->string('reference_type')->nullable(); // Model type for reference
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['section', 'status']);
            $table->index(['type', 'category']);
            $table->index(['created_by', 'created_at']);
            $table->index(['reference_type', 'reference_id']);

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
        Schema::dropIfExists('finance_invoices');
    }
};
