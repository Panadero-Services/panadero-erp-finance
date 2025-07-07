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
        // i1_languages table
        Schema::create('i1_languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 16)->default('Language');
            $table->string('identifier', 4)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_access_groups table
        Schema::create('i1_access_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 16)->unique();
            $table->string('icon_img', 80)->nullable();
            $table->integer('user_level')->default(99);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_customers table (system clients)
        Schema::create('i1_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 16)->default('Indigo Cust');
            $table->string('identifier', 4)->unique();
            $table->string('address', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->unsignedBigInteger('i1_country_id')->default(1);
            $table->unsignedBigInteger('i1_user_id')->default(2);
            $table->string('icon_img', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_statuses table
        Schema::create('i1_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 8)->default('status');
            $table->string('identifier', 1)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_config_categories table
        Schema::create('i1_config_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_config table
        Schema::create('i1_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('i1_config_category_id')->default(1);
            $table->string('key', 128)->unique();
            $table->text('value')->nullable();
            $table->text('option_field')->nullable();
            $table->text('privileges')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_users table (extended user management)
        Schema::create('i1_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 16)->unique();
            $table->string('full_name', 32)->nullable();
            $table->string('password', 64);
            $table->string('start_page', 64)->nullable();
            $table->string('email', 64)->nullable();
            $table->timestamp('reg_date')->nullable();
            $table->integer('access_level')->default(0);
            $table->unsignedBigInteger('i1_access_group_id')->nullable();
            $table->unsignedBigInteger('i1_language_id')->nullable();
            $table->unsignedBigInteger('i1_customer_id')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->text('details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // i1_tags table (navigation/menu structure)
        Schema::create('i1_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->unique();
            $table->string('id_column', 16)->default('id');
            $table->string('main_column', 32)->default('naam');
            $table->text('lingual')->nullable();
            $table->unsignedBigInteger('i1_user_id')->default(2);
            $table->unsignedBigInteger('i1_parent_id')->nullable();
            $table->string('icon_img', 80)->nullable();
            $table->integer('sequence')->nullable();
            $table->string('browse_file', 80)->default('webpage.php');
            $table->string('css_file', 80)->default('lib_indigo.css');
            $table->string('browse_class', 80)->default('veld_naam_middel');
            $table->integer('n_columns')->default(-1);
            $table->integer('rows_per_page')->default(200);
            $table->integer('record_detail_x_size')->default(666);
            $table->integer('record_detail_y_size')->default(800);
            $table->string('order_by', 80)->nullable();
            $table->text('visible_columns')->nullable();
            $table->string('access', 255)->default('(1:e)(2:e)(3:r)(11:r)(12:r)');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_table')->default(false);
            $table->boolean('is_in_toolbar')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i1_tags');
        Schema::dropIfExists('i1_users');
        Schema::dropIfExists('i1_config');
        Schema::dropIfExists('i1_config_categories');
        Schema::dropIfExists('i1_statuses');
        Schema::dropIfExists('i1_customers');
        Schema::dropIfExists('i1_access_groups');
        Schema::dropIfExists('i1_languages');
    }
}; 