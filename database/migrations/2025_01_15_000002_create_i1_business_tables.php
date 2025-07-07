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
        // i1_countries table
        Schema::create('i1_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->nullable();
            $table->string('identifier', 4)->unique();
            $table->decimal('max_temperature', 6, 2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_sites table
        Schema::create('i1_sites', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->string('name', 128)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->unsignedBigInteger('i1_country_id')->default(1);
            $table->string('email', 64)->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('i1_user_id')->default(2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_business_customers table
        Schema::create('i1_business_customers', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->string('name', 128)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->unsignedBigInteger('i1_country_id')->default(1);
            $table->string('email', 64)->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('i1_user_id')->default(2);
            $table->decimal('default_qty', 9, 2)->default(27);
            $table->unsignedBigInteger('i1_forwarder_id')->default(1);
            $table->unsignedBigInteger('i1_product_id')->default(1);
            $table->unsignedBigInteger('i1_source_site_id')->default(1);
            $table->string('docs_array', 255)->nullable();
            $table->string('analyse_array', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_suppliers table
        Schema::create('i1_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->string('name', 128)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->unsignedBigInteger('i1_country_id')->default(1);
            $table->string('email', 64)->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('i1_user_id')->default(2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_forwarders table
        Schema::create('i1_forwarders', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->string('name', 128)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->unsignedBigInteger('i1_country_id')->default(1);
            $table->string('email', 64)->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('i1_user_id')->default(2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_product_groups table
        Schema::create('i1_product_groups', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->string('name', 64)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_products table
        Schema::create('i1_products', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->string('name', 128)->nullable();
            $table->unsignedBigInteger('i1_product_group_id')->default(0);
            $table->decimal('purity', 9, 2)->default(100);
            $table->decimal('density', 9, 2)->default(1);
            $table->integer('un_code')->default(0);
            $table->string('un_nl', 64)->nullable();
            $table->string('un_fr', 64)->nullable();
            $table->string('un_uk', 64)->nullable();
            $table->string('un_de', 64)->nullable();
            $table->boolean('is_base_product')->default(true);
            $table->unsignedBigInteger('i1_base_product_id')->default(1);
            $table->unsignedBigInteger('i1_mix1_product_id')->nullable();
            $table->unsignedBigInteger('i1_mix2_product_id')->nullable();
            $table->unsignedBigInteger('i1_mix3_product_id')->nullable();
            $table->unsignedBigInteger('i1_balance_product_id')->nullable();
            $table->decimal('base_percentage', 9, 2)->default(100);
            $table->decimal('mix1_percentage', 9, 2)->default(0);
            $table->decimal('mix2_percentage', 9, 2)->default(0);
            $table->decimal('mix3_percentage', 9, 2)->default(0);
            $table->decimal('balance_percentage', 9, 2)->default(0);
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_storage table
        Schema::create('i1_storage', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 12)->unique();
            $table->unsignedBigInteger('i1_source_site_id')->default(0);
            $table->unsignedBigInteger('i1_product_id')->default(0);
            $table->decimal('max_qty', 9, 2)->nullable();
            $table->decimal('actual_qty', 9, 2)->nullable();
            $table->string('tag', 16)->nullable();
            $table->decimal('realtime_percentage', 9, 2)->nullable();
            $table->timestamp('realtime_update')->nullable();
            $table->string('units', 8)->nullable();
            $table->decimal('actual_density', 9, 2)->nullable();
            $table->decimal('actual_purity', 9, 2)->nullable();
            $table->integer('default_sequence')->default(0);
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_order_templates table
        Schema::create('i1_order_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('i1_customer_id')->default(0);
            $table->unsignedBigInteger('i1_forwarder_id')->default(0);
            $table->unsignedBigInteger('i1_source_site_id')->default(0);
            $table->unsignedBigInteger('i1_dest_site_id')->default(0);
            $table->unsignedBigInteger('i1_product_id')->default(0);
            $table->decimal('order_qty', 9, 2);
            $table->decimal('last_net_qty', 9, 2);
            $table->timestamp('last_a_date');
            $table->unsignedBigInteger('i1_storage_from_id')->default(0);
            $table->unsignedBigInteger('i1_storage_to_id')->default(0);
            $table->string('docs_array', 255)->nullable();
            $table->string('analyse_array', 255)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_orders table
        Schema::create('i1_orders', function (Blueprint $table) {
            $table->id();
            $table->string('tpt_order_nr', 16)->unique();
            $table->string('cust_order_nr', 16)->unique();
            $table->unsignedBigInteger('i1_customer_id')->nullable();
            $table->unsignedBigInteger('i1_supplier_id')->nullable();
            $table->timestamp('r_date')->nullable();
            $table->timestamp('e_date')->nullable();
            $table->timestamp('a_date')->nullable();
            $table->timestamp('b_date')->nullable();
            $table->unsignedBigInteger('i1_forwarder_id')->default(0);
            $table->unsignedBigInteger('i1_source_site_id')->default(0);
            $table->unsignedBigInteger('i1_dest_site_id')->default(0);
            $table->unsignedBigInteger('i1_product_id')->default(0);
            $table->decimal('order_qty', 9, 2)->nullable();
            $table->unsignedBigInteger('i1_storage_from_id')->default(0);
            $table->unsignedBigInteger('i1_storage_to_id')->default(0);
            $table->decimal('weight1', 9, 2)->nullable();
            $table->decimal('weight2', 9, 2)->nullable();
            $table->decimal('net_qty', 9, 2)->nullable();
            $table->decimal('density', 9, 2)->nullable();
            $table->decimal('purity', 9, 2)->nullable();
            $table->string('docs_array', 255)->nullable();
            $table->string('analyse_array', 255)->nullable();
            $table->unsignedBigInteger('i1_status_id')->default(1);
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i1_orders');
        Schema::dropIfExists('i1_order_templates');
        Schema::dropIfExists('i1_storage');
        Schema::dropIfExists('i1_products');
        Schema::dropIfExists('i1_product_groups');
        Schema::dropIfExists('i1_forwarders');
        Schema::dropIfExists('i1_suppliers');
        Schema::dropIfExists('i1_business_customers');
        Schema::dropIfExists('i1_sites');
        Schema::dropIfExists('i1_countries');
    }
}; 