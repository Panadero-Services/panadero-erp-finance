<?php
// app/Services/GameServerService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GameServerService
{
    private $config;
    private $masterUrl;

    public function __construct(array $config, string $masterUrl)
    {
        $this->config = $config;
        $this->masterUrl = $masterUrl;
    }

    public function startHeartbeat(): void
    {
        // Start heartbeat process
        while (true) {
            $this->sendHeartbeat();
            sleep($this->config['heartbeat_interval']);
        }
    }

    private function sendHeartbeat(): void
    {
        try {
            Http::post("{$this->masterUrl}/master/worlds/heartbeat", [
                'server_id' => $this->config['id'],
                'current_players' => $this->getCurrentPlayers(),
                'status' => 'online'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send heartbeat: ' . $e->getMessage());
        }
    }
}