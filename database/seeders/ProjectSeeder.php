<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'public',
                'description' => 'default project',
                'json' => '{"key":"value"}',
                'user_id' => 1,
                'is_active' => 1
            ]
        );
    }
}
