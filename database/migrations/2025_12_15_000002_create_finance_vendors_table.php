<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the finance_vendors table - extends the core vendors entity with financial-specific data
     */
    public function up(): void
    {
        Schema::create('finance_vendors', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to the core vendors table (shared entity)
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            
            // Financial-specific vendor information
            $table->string('payment_terms')->nullable(); // e.g., "Net 30", "2/10 Net 30"
            $table->integer('payment_terms_days')->nullable(); // Days for payment
            $table->decimal('credit_limit', 15, 2)->nullable(); // Maximum credit allowed
            $table->decimal('current_balance', 15, 2)->default(0.00); // Current outstanding balance
            
            // Banking and payment information
            $table->string('preferred_payment_method')->nullable(); // check, wire, ach, etc.
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->string('iban')->nullable(); // International banking
            $table->string('swift_code')->nullable(); // International banking
            
            // Tax and accounting
            $table->string('default_expense_account_code')->nullable(); // Link to chart of accounts
            $table->boolean('is_1099_vendor')->default(false); // US tax reporting
            $table->decimal('ytd_payments', 15, 2)->default(0.00); // Year-to-date payments
            $table->string('tax_classification')->nullable(); // For tax reporting
            
            // Purchase and procurement
            $table->json('preferred_categories')->nullable(); // Product/service categories
            $table->decimal('minimum_order_amount', 15, 2)->nullable();
            $table->boolean('requires_po')->default(false); // Requires purchase order
            $table->integer('lead_time_days')->nullable(); // Standard delivery time
            
            // Performance tracking
            $table->decimal('average_rating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 15, 2)->default(0.00); // Lifetime spending
            $table->date('last_order_date')->nullable();
            $table->date('last_payment_date')->nullable();
            
            // Finance module specific
            $table->boolean('auto_approve_invoices')->default(false);
            $table->decimal('auto_approve_limit', 15, 2)->nullable();
            $table->string('default_gl_account')->nullable(); // General ledger account
            $table->string('cost_center')->nullable();
            $table->string('department')->nullable();
            
            // Audit and compliance
            $table->json('required_documents')->nullable(); // List of required documents
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->boolean('background_check_required')->default(false);
            $table->boolean('insurance_required')->default(false);
            $table->date('insurance_expiry_date')->nullable();
            
            // System fields
            $table->json('finance_metadata')->nullable(); // Finance-specific additional data
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['vendor_id', 'is_active']);
            $table->index(['payment_terms_days', 'is_active']);
            $table->index(['credit_limit', 'current_balance']);
            $table->index(['is_1099_vendor', 'ytd_payments']);
            $table->index(['contract_end_date', 'is_active']);
            $table->index(['last_order_date', 'is_active']);
            $table->index('default_gl_account');
            $table->index(['cost_center', 'department']);
            
            // Ensure one finance record per vendor
            $table->unique('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_vendors');
    }
};

