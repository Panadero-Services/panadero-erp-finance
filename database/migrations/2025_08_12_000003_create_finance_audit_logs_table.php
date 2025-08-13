<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('finance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('entity');
            $table->string('action');
            $table->json('meta')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('finance_audit_logs');
    }
};