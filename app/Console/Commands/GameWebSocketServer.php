<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\GameWebSocketController;

class GameWebSocketServer extends Command
{
    protected $signature = 'game:serve';
    protected $description = 'Start the game WebSocket server';

    public function handle()
    {
        $this->info('Starting game WebSocket server...');
        
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new GameWebSocketController()
                )
            ),
            8090
        );

        $this->info('WebSocket server running on port 8090');
        $server->run();
    }
} 