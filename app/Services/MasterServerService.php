<?php
// app/Services/MasterServerService.php

namespace App\Services;

use App\Models\GameWorld;
use App\Models\Player;
use App\Models\PlayerResource;
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

    public function getSharedPlayerState(string $playerId): array
    {
        // Get player from any world
        $player = Player::where('player_id', $playerId)->first();
        
        if (!$player) {
            return [
                'resources' => ['gold' => 0, 'water' => 0, 'kryptonite' => 0],
                'score' => 0,
                'rank' => null
            ];
        }

        // Get shared resources
        $resources = PlayerResource::where('player_id', $player->id)->first();
        
        // Get global score
        $score = $player->total_score ?? 0;
        
        // Get global rank
        $rank = $this->getPlayerRank($player->id);

        return [
            'resources' => [
                'gold' => $resources->gold ?? 0,
                'water' => $resources->water ?? 0,
                'kryptonite' => $resources->kryptonite ?? 0
            ],
            'score' => $score,
            'rank' => $rank
        ];
    }

    public function updateSharedPlayerState(string $playerId, array $data): void
    {
        $player = Player::where('player_id', $playerId)->firstOrCreate([
            'player_id' => $playerId,
            'name' => $data['name'] ?? 'Unknown Player'
        ]);

        // Update shared resources
        PlayerResource::updateOrCreate(
            ['player_id' => $player->id],
            [
                'gold' => $data['resources']['gold'] ?? 0,
                'water' => $data['resources']['water'] ?? 0,
                'kryptonite' => $data['resources']['kryptonite'] ?? 0
            ]
        );

        // Update global score
        if (isset($data['score'])) {
            $player->update(['total_score' => $data['score']]);
        }

        // Cache the shared state
        Cache::put("player_state:{$playerId}", $data, now()->addHours(1));
    }

    public function transferPlayerBetweenWorlds(string $playerId, string $fromWorldId, string $toWorldId): bool
    {
        // End session in current world
        PlayerSession::where('player_id', $playerId)
            ->where('game_world_id', $fromWorldId)
            ->where('status', 'active')
            ->update(['status' => 'transferred']);

        // Start session in new world
        PlayerSession::create([
            'player_id' => $playerId,
            'game_world_id' => $toWorldId,
            'status' => 'active',
            'joined_at' => now(),
            'transfer_data' => [
                'from_world' => $fromWorldId,
                'transfer_time' => now()->toISOString()
            ]
        ]);

        return true;
    }
}