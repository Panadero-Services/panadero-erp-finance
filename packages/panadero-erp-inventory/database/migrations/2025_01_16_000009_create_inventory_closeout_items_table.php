<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_closeout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('closeout_id')->constrained('inventory_closeouts');
            $table->string('sku');
            $table->string('name');
            $table->decimal('start_stock', 15, 4)->default(0);
            $table->decimal('incoming', 15, 4)->default(0);
            $table->decimal('outgoing', 15, 4)->default(0);
            $table->decimal('adjustments', 15, 4)->default(0);
            $table->decimal('end_stock', 15, 4)->default(0);
            $table->decimal('variance', 15, 4)->default(0);
            $table->enum('status', ['balanced', 'variance'])->default('balanced');
            $table->timestamps();
            
            $table->index(['closeout_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_closeout_items');
    }
};
