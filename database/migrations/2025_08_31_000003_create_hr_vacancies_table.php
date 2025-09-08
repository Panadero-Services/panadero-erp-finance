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
        Schema::create('hr_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->string('position_type')->nullable(); // job title
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern', 'consultant'])->default('full_time');
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'executive'])->default('mid');
            $table->string('location')->nullable();
            $table->boolean('remote_allowed')->default(false);
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('required_skills')->nullable();
            $table->json('preferred_skills')->nullable();
            $table->json('benefits')->nullable();
            $table->date('application_deadline')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('hiring_manager_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->integer('max_applications')->nullable();
            $table->integer('current_applications')->default(0);
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('hr_departments')->onDelete('cascade');
            $table->foreign('hiring_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_vacancies');
    }
};
