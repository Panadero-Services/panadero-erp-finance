<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173', 
        'http://127.0.0.1:5173',
        'http://192.168.2.20:8000',
        'http://192.168.2.9:5173',
        'http://192.168.2.9:3001',
        'http://192.168.2.9:8000',
        'http://192.168.2.20:5173',
        'http://192.168.2.20:3000',  // Add this for the game server
        'http://84.80.133.32:8000',  // Add this
        'http://84.80.133.32:5173',  // Add this
        'http://84.80.133.32:8000',  // Add this
        'http://84.80.133.32:3000',  // Add this
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
]; 