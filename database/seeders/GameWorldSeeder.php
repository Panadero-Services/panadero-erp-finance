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
                'width' => 40000,
                'height' => 40000
            ],
            'borderForce' => [
                'radius' => 500,
                'strength' => 0.5
            ]
        ]);

        $worlds = [
            [
                'name' => 'GameServer 1 LAN',
                'server_url' => 'http://192.168.2.20:3000',
                'server_id' => 'local-01',
                'description' => 'Local connection to GameServer1',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GameServer 2 LAN',
                'server_url' => 'http://192.168.2.9:3001',
                'server_id' => 'lan-02',
                'description' => 'Local connection to GameServer2',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GameServer 1 WWW',
                'server_url' => 'http://84.80.133.32:3000',
                'server_id' => 'ww-03',
                'description' => 'External connection to GameServer1',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GameServer 2 WWW',
                'server_url' => 'http://84.80.133.32:3001',
                'server_id' => 'ww-04',
                'description' => 'External connection to GameServer2',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
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