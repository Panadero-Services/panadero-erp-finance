<?php
// config/game.php

return [
    'roles' => [
        'is_master_server' => env('IS_MASTER_SERVER', false),
        'is_game_server' => env('IS_GAME_SERVER', false),
    ],
    
    'master_server' => [
        'url' => env('MASTER_SERVER_URL', 'http://localhost:8000'),
        'port' => env('MASTER_SERVER_PORT', 8000),
        'heartbeat_interval' => env('MASTER_HEARTBEAT_INTERVAL', 30), // seconds
        'heartbeat_timeout' => env('MASTER_HEARTBEAT_TIMEOUT', 90),   // seconds
    ],
    
    'game_server' => [
        'id' => env('GAME_SERVER_ID', 'local-dev-01'),
        'port' => env('GAME_SERVER_PORT', 3000),
        'url' => env('GAME_SERVER_URL', 'http://localhost:3000'),
        'max_players' => env('GAME_SERVER_MAX_PLAYERS', 100),
    ]
];
?>