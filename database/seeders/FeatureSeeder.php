<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'item' => 'Grid',
                'title' => 'Grid feature',
                'description' => 'A powerful data grid system that enables efficient content browsing, sorting, and filtering with real-time updates and customizable views.',
                'color' => 'blue',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Grid sample', 'url' => '/grid', 'route' => 'grid']
                ]),
                'links' => json_encode([]),
                'path' => '/features/grid',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ],
            [
                'item' => 'Event',
                'title' => 'Scheduler feature',
                'description' => 'Advanced event management system with calendar integration, reminders, and automated scheduling capabilities for optimal resource allocation.',
                'color' => 'green',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard']
                ]),
                'links' => json_encode([]),
                'path' => '/features/event',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ],
            [
                'item' => 'Mood',
                'title' => 'Mood feature',
                'description' => 'Intelligent task management platform with mood-based prioritization, progress tracking, and collaborative todo lists for enhanced productivity.',
                'color' => 'purple',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard']
                ]),
                'links' => json_encode([]),
                'path' => '/features/mood',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ],
            [
                'item' => 'Calc',
                'title' => 'Spreadsheet feature',
                'description' => 'Dynamic spreadsheet solution with advanced calculation capabilities, data visualization, and real-time collaboration features for complex data analysis.',
                'color' => 'teal',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard']
                ]),
                'links' => json_encode([]),
                'path' => '/features/calc',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ],
            [
                'item' => 'Proj',
                'title' => 'Project feature',
                'description' => 'Comprehensive project management tool with milestone tracking, resource allocation, and team collaboration features for successful project delivery.',
                'color' => 'yellow',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard']
                ]),
                'links' => json_encode([]),
                'path' => '/features/proj',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ],
            [
                'item' => 'Contract',
                'title' => 'Contract feature',
                'description' => 'Web3 contract management system for secure smart contract deployment, monitoring, and interaction with blockchain networks.',
                'color' => 'indigo',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard']
                ]),
                'links' => json_encode([]),
                'path' => '/features/contract',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ],
            [
                'item' => 'Gantt',
                'title' => 'Gantt feature',
                'description' => 'Interactive Gantt chart system for project planning, timeline visualization, and dependency management with drag-and-drop functionality.',
                'color' => 'red',
                'version' => '1.0',
                'icon' => 'ProjectIcon',
                'options' => json_encode([
                    ['name' => 'Demo', 'url' => '/home/dashboard', 'route' => 'home/dashboard']
                ]),
                'links' => json_encode([]),
                'path' => '/features/gantt',
                'status' => 'featured',
                'progress' => 100,
                'is_active' => true
            ]
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(
                ['item' => $feature['item']],
                $feature
            );
        }
    }
} 