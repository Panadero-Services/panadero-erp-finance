<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameWorldSeeder extends Seeder
{
    public function run(): void
    {
        $defaultConfig = json_encode([
            'worldSize' => [
                'width' => 10000,
                'height' => 10000
            ],
            'borderForce' => [
                'radius' => 500,
                'strength' => 0.5
            ]
        ]);

        $worlds = [
            [
                'name' => 'Local Development World',
                'server_url' => 'http://localhost:3000',
                'server_id' => 'local-dev-01',
                'description' => 'Local development game server for testing',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed: No heartbeat for offline servers
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Local LAN Development World I',
                'server_url' => 'http://192.168.2.20:3000',
                'server_id' => 'local-lan-01',
                'description' => 'Local LAN iMacPro server for testing',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Local LAN Development World II',
                'server_url' => 'http://192.168.2.9:3000',
                'server_id' => 'local-lan-02',
                'description' => 'Local LAN macBookPro server for testing',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Web World I',
                'server_url' => 'http://84.80.133.32:3000',
                'server_id' => 'ww-01',
                'description' => 'Online world for testing 1',
                'status' => 'offline',
                'max_players' => 12,
                'current_players' => 0,
                'world_config' => json_encode([
                    'worldSize' => [
                        'width' => 40000,
                        'height' => 40000
                    ],
                    'borderForce' => [
                        'radius' => 500,
                        'strength' => 0.5
                    ]
                ]),
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($worlds as $world) {
            DB::table('game_worlds')->insert($world);
        }
    }
} 