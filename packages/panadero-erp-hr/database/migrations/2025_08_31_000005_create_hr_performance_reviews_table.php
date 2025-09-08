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
        Schema::create('hr_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->string('review_period');
            $table->date('review_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('goals')->nullable();
            $table->json('achievements')->nullable();
            $table->json('skills_assessment')->nullable();
            $table->integer('overall_rating')->nullable(); // 1-5 scale
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('manager_comments')->nullable();
            $table->text('employee_comments')->nullable();
            $table->json('development_plan')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'completed', 'acknowledged'])->default('draft');
            $table->timestamp('employee_acknowledged_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_performance_reviews');
    }
};
