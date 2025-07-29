<?php
// app/Services/MasterServerService.php

namespace App\Services;

use App\Models\GameWorld;
use App\Models\PlayerSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class MasterServerService
{
    private int $heartbeatInterval;
    private int $heartbeatTimeout;

    public function __construct(int $heartbeatInterval, int $heartbeatTimeout)
    {
        $this->heartbeatInterval = $heartbeatInterval;
        $this->heartbeatTimeout = $heartbeatTimeout;
    }

    public function updateWorldHeartbeat(string $serverId, array $data): void
    {
        $world = GameWorld::where('server_id', $serverId)->first();
        
        if (!$world) {
            Log::warning("Heartbeat received from unregistered server: {$serverId}");
            return;
        }

        $world->update([
            'status' => $data['status'] ?? 'online',
            'current_players' => $data['current_players'] ?? 0,
            'last_heartbeat' => now()
        ]);

        // Cache the world status for quick access
        Cache::put("game_world:{$serverId}:status", $world->only([
            'status', 
            'current_players', 
            'last_heartbeat'
        ]), now()->addMinutes(5));
    }

    public function registerWorld(array $data): GameWorld
    {
        $world = GameWorld::create([
            'name' => $data['name'],
            'server_id' => $data['server_id'],
            'server_url' => $data['server_url'],
            'description' => $data['description'] ?? null,
            'status' => 'online',
            'max_players' => $data['max_players'] ?? 100,
            'current_players' => 0,
            'world_config' => $data['world_config'] ?? [],
            'last_heartbeat' => now()
        ]);

        Event::dispatch('game.world.registered', $world);

        return $world;
    }

    public function unregisterWorld(string $serverId): bool
    {
        $world = GameWorld::where('server_id', $serverId)->first();
        
        if (!$world) {
            return false;
        }

        // Update any active sessions
        PlayerSession::where('game_world_id', $world->id)
            ->where('status', 'active')
            ->update([
                'status' => 'disconnected',
                'recovery_data' => [
                    'reason' => 'server_shutdown',
                    'timestamp' => now()->timestamp
                ]
            ]);

        $world->update(['status' => 'offline']);
        
        Event::dispatch('game.world.unregistered', $world);

        return true;
    }

    public function checkWorldHealth(GameWorld $world): bool
    {
        $lastHeartbeat = $world->last_heartbeat;
        $now = now();

        if ($lastHeartbeat->diffInSeconds($now) > $this->heartbeatTimeout) {
            $this->markWorldOffline($world);
            return false;
        }

        return true;
    }

    private function markWorldOffline(GameWorld $world): void
    {
        $world->update(['status' => 'offline']);
        
        // Handle active sessions
        PlayerSession::where('game_world_id', $world->id)
            ->where('status', 'active')
            ->update([
                'status' => 'disconnected',
                'recovery_data' => [
                    'reason' => 'server_timeout',
                    'timestamp' => now()->timestamp
                ]
            ]);

        Event::dispatch('game.world.offline', $world);
    }
}