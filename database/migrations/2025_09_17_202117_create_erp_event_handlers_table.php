<?php
// 2025_09_17_202117_create_erp_event_handlers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_event_handlers', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('action');
            $table->timestamp('start_plan')->nullable();
            $table->timestamp('end_plan')->nullable();
            $table->timestamp('start_actual')->nullable();
            $table->timestamp('end_actual')->nullable();
            $table->timestamp('due')->nullable();
            $table->timestamps();
            $table->json('json')->nullable();
            $table->text('comment')->nullable();
            
            $table->index(['module', 'action']);
            $table->index(['due', 'start_actual']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_event_handlers');
    }
};