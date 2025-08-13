<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7); // YYYY-MM
            $table->timestamps();
            $table->unique('period');
        });

        Schema::create('finance_budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('finance_budgets')->onDelete('cascade');
            $table->string('account');
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();
            $table->unique(['budget_id','account']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('finance_budget_lines');
        Schema::dropIfExists('finance_budgets');
    }
};