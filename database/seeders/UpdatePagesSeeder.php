<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class UpdatePagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = Page::all();

        foreach ($pages as $page) {
            // Update default values if they are empty or null
            if (empty($page->slogan)) {
                $page->slogan = 'Default Home Slogan';
            }
            if (empty($page->settings)) {
                $page->settings = '{}';
            }
            if (empty($page->type)) {
                $page->type = 'content';
            }
            if (empty($page->icon)) {
                $page->icon = 'view-dashboard-outline';
            }
            if (empty($page->image)) {
                $page->image = 'new2.jpg';
            }
            
            // Set default boolean values if they are null
            if ($page->is_active === null) {
                $page->is_active = true;
            }
            if ($page->sidebar === null) {
                $page->sidebar = false;
            }
            if ($page->header === null) {
                $page->header = false;
            }
            if ($page->footer === null) {
                $page->footer = false;
            }
            if ($page->public === null) {
                $page->public = false;
            }
            if ($page->animate === null) {
                $page->animate = false;
            }
            if ($page->max_width === null) {
                $page->max_width = false;
            }

            $page->save();
        }
    }
} 