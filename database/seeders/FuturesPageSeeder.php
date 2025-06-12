<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class FuturesPageSeeder extends Seeder
{
    public function run(): void
    {
        Page::create([
            'title' => 'home/futures',
            'file' => 'home/Futures.vue',
            'icon' => 'view-dashboard-outline',
            'image' => '',
            'slogan' => 'Just do it, innovation at the heart of your businesss',
            'type' => 'table',
            'settings' => '{}',
            'user_id' => 1,
            'project_id' => 1,
            'is_active' => 1,
            'sidebar' => 1,
            'header' => 1,
            'footer' => 1,
            'public' => 0,
            'animate' => 0,
            'max_width' => 100
        ]);
    }
} 