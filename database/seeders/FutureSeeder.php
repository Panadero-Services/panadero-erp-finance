<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Future;
use App\Models\User;
use App\Models\Project;

class FutureSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        if ($users->isEmpty() || $projects->isEmpty()) {
            $this->command->error('Users or Projects not found. Please run their seeders first.');
            return;
        }

        $statuses = ['idle', 'in_progress', 'completed', 'blocked', 'review'];
        $colors = ['blue', 'green', 'red', 'yellow', 'purple', 'orange'];
        $versions = ['v0.0.1', 'v0.1.0', 'v1.0.0', 'v1.1.0', 'v2.0.0'];
        $icons = ['rocket', 'star', 'lightbulb', 'flag', 'trophy', 'gem'];

        for ($i = 1; $i <= 50; $i++) {
            Future::create([
                'item' => 'future-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'title' => 'Future Feature ' . $i,
                'description' => 'This is a detailed description for future feature ' . $i . '. It includes various aspects and considerations for implementation.',
                'color' => $colors[array_rand($colors)],
                'version' => $versions[array_rand($versions)],
                'icon' => $icons[array_rand($icons)],
                'status' => $statuses[array_rand($statuses)],
                'user_id' => $users->random()->id,
                'project_id' => $projects->random()->id,
                'json' => [
                    'priority' => rand(1, 5),
                    'complexity' => rand(1, 5),
                    'estimated_hours' => rand(4, 40),
                    'tags' => ['feature', 'enhancement', 'optimization']
                ],
                'links' => [
                    [
                        'type' => 'relates_to',
                        'link_id' => '1',
                        'link_title' => 'Future Feature 1'
                    ]
                ],
                'is_active' => rand(0, 1),
                'is_locked' => rand(0, 1)
            ]);
        }
    }
} 