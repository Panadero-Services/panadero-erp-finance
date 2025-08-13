<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('finance_fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('cost', 14, 2);
            $table->date('acquired_at')->nullable();
            $table->enum('method', ['straight_line'])->default('straight_line');
            $table->unsignedInteger('useful_life_months')->default(36);
            $table->decimal('accumulated_depreciation', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('finance_asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('finance_fixed_assets')->onDelete('cascade');
            $table->string('period', 7); // YYYY-MM
            $table->decimal('amount', 14, 2);
            $table->timestamps();
            $table->unique(['fixed_asset_id','period']);
        });

        Schema::create('finance_asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('finance_fixed_assets')->onDelete('cascade');
            $table->date('disposed_at');
            $table->decimal('proceeds', 14, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('finance_asset_disposals');
        Schema::dropIfExists('finance_asset_depreciations');
        Schema::dropIfExists('finance_fixed_assets');
    }
};