<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['vendor', 'customer', 'forwarder']);
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->date('last_order_date')->nullable();
            $table->json('services')->nullable(); // For forwarders
            $table->decimal('on_time_delivery', 5, 2)->nullable(); // For forwarders
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status']);
            $table->index(['name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_partners');
    }
};
