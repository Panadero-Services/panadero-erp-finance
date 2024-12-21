<?php

use App\Models\Page;
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
        Schema::create('pages', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('title',80);
        $table->string('file',80);
        $table->string('icon',80);
        $table->string('image',80);
        $table->string('slogan',120);
        $table->string('type',80);
        $table->text('settings');
        $table->foreignId('user_id')->index();
        $table->foreignId('project_id')->index();
        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        $table->boolean('is_active');
        $table->boolean('sidebar');
        $table->boolean('header');
        $table->boolean('footer');
        $table->boolean('public');
        $table->boolean('animate');
        $table->integer('max_width');
        });

        Page::insert([
            'title' => 'Home',
            'file' => 'welcome.vue',
            'icon' => 'view-dashboard-outline',
            'image' => 'h-80 text-4xl text-black bg-contain bg-[url(../images/new2.jpg)]',
            'slogan' => 'Default Home Slogan',
            'type' => 'Home',
            'settings' => 'Home',
            'user_id' => 1,
            'project_id' => 1,
            'is_active' => 1,
            'sidebar' => 1,
            'header' => 1,
            'footer' => 1,
            'public' => 1,
            'animate' => 1,
            'max_width' => 1
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
