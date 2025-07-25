<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameWorldSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('game_worlds')->insert([
            [
                'name' => 'Local Development World',
                'server_url' => 'http://localhost:3000',
                'server_id' => 'local-dev-01',
                'description' => 'Local development game server for testing',
                'status' => 'online',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => json_encode([
                    'worldSize' => [
                        'width' => 10000,
                        'height' => 10000
                    ],
                    'borderForce' => [
                        'radius' => 500,
                        'strength' => 0.5
                    ]
                ]),
                'last_heartbeat' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
} 