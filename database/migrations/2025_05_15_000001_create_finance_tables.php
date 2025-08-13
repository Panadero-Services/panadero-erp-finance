<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chart of Accounts
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();  // Account code (e.g., 1000)
            $table->string('name');                // Account name
            $table->string('type');                // asset, liability, equity, revenue, expense
            $table->string('subtype')->nullable(); // More specific categorization
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Journal Entries
        Schema::create('finance_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->date('entry_date');
            $table->string('type');              // general, adjustment, closing
            $table->text('description');
            $table->string('period');            // YYYY-MM format
            $table->boolean('is_posted')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Journal Entry Lines
        Schema::create('finance_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('finance_journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('finance_accounts');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Accounts Payable
        Schema::create('finance_payables', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('vendor_id')->constrained('i1_suppliers');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('status');  // draft, approved, paid, partial, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Accounts Receivable
        Schema::create('finance_receivables', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('customer_id')->constrained('i1_business_customers');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('received_amount', 15, 2)->default(0);
            $table->string('status');  // draft, sent, partial, paid, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tax Records
        Schema::create('finance_tax_records', function (Blueprint $table) {
            $table->id();
            $table->string('tax_period');        // YYYY-MM or YYYY-QN format
            $table->string('tax_type');          // VAT, income, etc.
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->date('filing_due_date');
            $table->date('payment_due_date');
            $table->string('status');            // pending, filed, paid
            $table->timestamp('filed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Cash Flow Categories
        Schema::create('finance_cashflow_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');              // operating, investing, financing
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Cash Flow Transactions
        Schema::create('finance_cashflow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('finance_cashflow_categories');
            $table->date('transaction_date');
            $table->decimal('amount', 15, 2);
            $table->string('type');              // inflow, outflow
            $table->text('description');
            $table->string('reference_no')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Financial Periods
        Schema::create('finance_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period');            // YYYY-MM format
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status');            // open, closed
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_periods');
        Schema::dropIfExists('finance_cashflow_transactions');
        Schema::dropIfExists('finance_cashflow_categories');
        Schema::dropIfExists('finance_tax_records');
        Schema::dropIfExists('finance_receivables');
        Schema::dropIfExists('finance_payables');
        Schema::dropIfExists('finance_journal_lines');
        Schema::dropIfExists('finance_journal_entries');
        Schema::dropIfExists('finance_accounts');
    }
};
