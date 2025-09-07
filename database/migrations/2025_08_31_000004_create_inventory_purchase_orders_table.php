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
        Schema::create('inventory_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique()->index();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name'); // Denormalized for performance
            $table->date('order_date');
            $table->date('expected_delivery');
            $table->string('status')->default('draft'); // draft, pending, approved, completed, cancelled
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('items')->nullable(); // Store PO items as JSON
            $table->json('metadata')->nullable(); // Store additional PO data
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['supplier_id', 'status']);
            $table->index(['status', 'order_date']);
            $table->index(['created_by', 'created_at']);

            // Foreign keys
            $table->foreign('supplier_id')->references('id')->on('inventory_suppliers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_purchase_orders');
    }
};
