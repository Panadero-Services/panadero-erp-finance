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
                'title' => 'Public Project',
                'description' => 'Default public project for general use',
                'color' => 'blue',
                'status' => 'idle',
                'json' => json_encode(['key' => 'value']),
                'links' => json_encode([]),
                'user_id' => 1,
                'is_active' => true
            ]
        );

        // Create additional sample projects
        $colors = ['blue', 'green', 'red', 'yellow', 'purple', 'orange'];
        $statuses = ['idle', 'in_progress', 'completed', 'blocked', 'review'];
        
        for ($i = 2; $i <= 10; $i++) {
            Project::updateOrCreate(
                ['id' => $i],
                [
                    'title' => 'Project ' . $i,
                    'description' => 'Sample project description ' . $i,
                    'color' => $colors[array_rand($colors)],
                    'status' => $statuses[array_rand($statuses)],
                    'json' => ['priority' => rand(1, 5), 'category' => 'sample'],
                    'links' => [],
                    'user_id' => rand(1, 15), // Assuming you have users with IDs 1-15
                    'is_active' => rand(0, 1)
                ]
            );
        }
    }
}
