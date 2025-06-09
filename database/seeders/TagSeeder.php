<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'AI'],
            ['name' => 'Web3'],
            ['name' => 'Development'],
            ['name' => 'Agents'],
            ['name' => 'Team'],
            ['name' => 'Project'],
            ['name' => 'Technology'],
        ];

        foreach ($tags as $tag) {
            Tag::create(array_merge($tag, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
} 