<?php
// app/Console/Commands/MonitorServers.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GameWorld;
use App\Services\MasterServerService;

class MonitorServers extends Command
{
    protected $signature = 'game:monitor-servers';
    protected $description = 'Monitor game server health via heartbeats';

    private MasterServerService $masterServerService;

    public function __construct(MasterServerService $masterServerService)
    {
        parent::__construct();
        $this->masterServerService = $masterServerService;
    }

    public function handle()
    {
        $this->info('Starting server health monitoring...');

        GameWorld::where('status', '!=', 'offline')
            ->each(function ($world) {
                $this->info("Checking health of server {$world->server_id}");
                
                if (!$this->masterServerService->checkWorldHealth($world)) {
                    $this->warn("Server {$world->server_id} marked as offline due to missed heartbeats");
                }
            });

        $this->info('Server health monitoring completed');
    }
}