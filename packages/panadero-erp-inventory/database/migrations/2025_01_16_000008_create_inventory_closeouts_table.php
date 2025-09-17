<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_closeouts', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // 2025-01, 2025-W03, etc.
            $table->enum('type', ['monthly', 'weekly', 'daily']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft');
            $table->decimal('total_start_value', 15, 2)->default(0);
            $table->decimal('total_incoming_value', 15, 2)->default(0);
            $table->decimal('total_outgoing_value', 15, 2)->default(0);
            $table->decimal('total_adjustments_value', 15, 2)->default(0);
            $table->decimal('total_end_value', 15, 2)->default(0);
            $table->decimal('total_variance', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['period', 'type']);
            $table->index(['status', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_closeouts');
    }
};
