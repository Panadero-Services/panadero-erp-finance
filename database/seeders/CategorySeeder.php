<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::updateOrCreate(
            ['slug' => 'technology'],
            [
                'name' => 'Technology',
                'description' => 'All about tech.'
            ]
        );
        Category::updateOrCreate(
            ['slug' => 'business'],
            [
                'name' => 'Business',
                'description' => 'Business insights and news.'
            ]
        );
        Category::updateOrCreate(
            ['slug' => 'lifestyle'],
            [
                'name' => 'Lifestyle',
                'description' => 'Tips and stories for a better life.'
            ]
        );
    }
}
