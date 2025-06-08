<?php

namespace Database\Seeders;

use App\Models\PostType;
use Illuminate\Database\Seeder;

class PostTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'post',
                'text_length' => 1024,
            ],
            [
                'name' => 'article',
                'text_length' => 4096,
            ],
            [
                'name' => 'script',
                'text_length' => 2024,
            ],
        ];

        foreach ($types as $type) {
            PostType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
} 