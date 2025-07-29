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
                'name' => 'Localhost I',
                'server_url' => 'http://192.168.2.20:3000',
                'server_id' => 'local-01',
                'description' => 'Localhost iMacPro server for elementary connection',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Local Network LAN I',
                'server_url' => 'http://192.168.2.9:3000',
                'server_id' => 'lan-02',
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
                'server_id' => 'ww-03',
                'description' => 'External connection to local-01',
                'status' => 'offline',
                'max_players' => 100,
                'current_players' => 0,
                'world_config' => $defaultConfig,
                'last_heartbeat' => null,  // Changed
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Web World II',
                'server_url' => 'http://84.80.133.32:3001',
                'server_id' => 'ww-04',
                'description' => 'External connection to lan-02',
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