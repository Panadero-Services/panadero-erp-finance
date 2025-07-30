<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\Player;
use App\Models\GameWorld;
use App\Models\PlayerResource;
use App\Models\PlayerWorldSession;
use App\Models\WorldTransfer;
use App\Models\WorldEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\ScoreUpdated;
use App\Events\WorldTransferCompleted;
use App\Events\PlayerJoinedWorld;
use App\Events\PlayerLeftWorld;
use App\Services\MasterServerService;

class MasterGameServerController extends Controller
{
    protected $gameState;
    protected $leaderboardCacheKey = 'game_leaderboard';
    protected $leaderboardCacheDuration = 300; // 5 minutes

    private const HEARTBEAT_INTERVAL = 30;  // seconds
    private const HEARTBEAT_GRACE_PERIOD = 5;  // seconds
    private const MAX_MISSED_HEARTBEATS = 3;

    public function __construct()
    {
        $this->gameState = [
            'active_players' => 0,
            'total_games_played' => 0,
            'server_start_time' => now(),
            'current_round' => 1
        ];
    }

    /**
     * Get comprehensive game state including worlds and players
     */
    public function getGameState(Request $request)
    {
        $playerName = $request->input('player_name');
        $player = $playerName ? Player::where('name', $playerName)->first() : null;
        
        $gameState = [
            'server' => [
                'status' => 'online',
                'uptime' => now()->diffInSeconds($this->gameState['server_start_time']),
                'active_players' => $this->getActivePlayerCount(),
                'total_games_played' => $this->getTotalGamesPlayed(),
                'current_round' => $this->gameState['current_round']
            ],
            'worlds' => $this->getWorldsStatus(),
            'player' => $player ? $this->buildPlayerStats($player) : null,
            'leaderboard' => $this->getLeaderboard($request),
            'global_stats' => $this->getGlobalStats()
        ];

        return response()->json($gameState);
    }


    public function getWorldsStatuz(Request $request)
    {
        return $request;
    }



    /**
     * Get all game worlds status
     */
    public function getWorldsStatus()
    {
        return GameWorld::all()->map(function ($world) {
            return [
                'id' => $world->id,
                'name' => $world->name,
                'server_id' => $world->server_id,
                'server_url' => $world->server_url,
                'status' => $world->status,
                'is_online' => $world->isOnline(),
                'max_players' => $world->max_players,
                'current_players' => $world->current_players,
                'available_capacity' => $world->getAvailableCapacity(),
                'last_heartbeat' => $world->last_heartbeat,
                'description' => $world->description
            ];
        });
    }

    /**
     * Register a new game world
     */
    public function registerWorld(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:game_worlds',
            'server_url' => 'required|url',
            'server_id' => 'required|string|max:50|unique:game_worlds',
            'description' => 'nullable|string',
            'max_players' => 'integer|min:1|max:1000',
            'world_config' => 'nullable|array'
        ]);

        $world = GameWorld::create([
            'name' => $request->name,
            'server_url' => $request->server_url,
            'server_id' => $request->server_id,
            'description' => $request->description,
            'max_players' => $request->max_players ?? 100,
            'world_config' => $request->world_config,
            'status' => 'online',
            'last_heartbeat' => now()
        ]);

        return response()->json([
            'success' => true,
            'world' => $world,
            'message' => 'Game world registered successfully'
        ]);
    }

    /**
     * Update world heartbeat and status
     */
    public function updateWorldHeartbeat(Request $request)
    {
        $request->validate([
            'server_id' => 'required|string',
            'current_players' => 'required|integer|min:0',
            'status' => 'nullable|in:online,offline,maintenance'
        ]);

        $world = GameWorld::where('server_id', $request->server_id)->first();
        
        if (!$world) {
            return response()->json(['error' => 'World not found'], 404);
        }

        // Update world state
        $world->update([
            'current_players' => $request->current_players,
            'last_heartbeat' => now()
        ]);

        // Always update status when receiving heartbeat
        if ($world->status !== GameWorld::STATUS_ONLINE) {
            $world->updateServerState(
                GameWorld::STATUS_ONLINE,
                'Heartbeat received'
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Player joins a world
     */
    public function playerJoinWorld(Request $request)
    {
        $request->validate([
            'world_id' => 'required|exists:game_worlds,id',
            'player_name' => 'required|string|max:100',
            'session_id' => 'required|string|max:100|unique:player_world_sessions',
            'player_state' => 'nullable|array'
        ]);

        $world = GameWorld::findOrFail($request->world_id);
        
        // Get or create player
        $player = Player::firstOrCreate(
            ['name' => $request->player_name],
            [
                'callsign' => $request->player_name,
                'last_login' => now(),
                'is_active' => true
            ]
        );

        // Check if world is online and has capacity
        if (!$world->isOnline()) {
            return response()->json(['error' => 'World is offline'], 400);
        }

        if ($world->getAvailableCapacity() <= 0) {
            return response()->json(['error' => 'World is full'], 400);
        }

        // Disconnect from any other world first
        PlayerWorldSession::where('player_id', $player->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'disconnected_at' => now()]);

        // Create new session
        $session = PlayerWorldSession::create([
            'player_id' => $player->id,
            'game_world_id' => $world->id,
            'session_id' => $request->session_id,
            'player_name' => $request->player_name,
            'player_state' => $request->player_state ?? [],
            'is_active' => true
        ]);

        // Update world player count
        $world->updatePlayerCount($world->current_players + 1);

        // Update player last login
        $player->updateLastLogin();

        // Broadcast player joined event
        broadcast(new PlayerJoinedWorld($session))->toOthers();

        return response()->json([
            'success' => true,
            'session' => $session,
            'world' => $world,
            'player' => $player
        ]);
    }

    /**
     * Player leaves a world
     */
    public function playerLeaveWorld(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        $session = PlayerWorldSession::where('session_id', $request->session_id)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $world = $session->gameWorld;
        
        // Disconnect session
        $session->disconnect();

        // Update world player count
        $world->updatePlayerCount(max(0, $world->current_players - 1));

        // Broadcast player left event
        broadcast(new PlayerLeftWorld($session))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Transfer player to another world (WARP)
     */
    public function transferToWorld(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string',
            'target_world_id' => 'required|exists:game_worlds,id',
            'transfer_type' => 'required|in:warp,teleport,emergency',
            'resources_to_transfer' => 'nullable|array',
            'player_state' => 'nullable|array'
        ]);

        $player = Player::where('name', $request->player_name)->first();
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $targetWorld = GameWorld::findOrFail($request->target_world_id);

        // Check if target world is available
        if (!$targetWorld->isOnline()) {
            return response()->json(['error' => 'Target world is offline'], 400);
        }

        if ($targetWorld->getAvailableCapacity() <= 0) {
            return response()->json(['error' => 'Target world is full'], 400);
        }

        // Get current session
        $currentSession = $player->activeSession;
        $fromWorldId = $currentSession?->game_world_id;

        // Disconnect from current world
        if ($currentSession) {
            $currentSession->disconnect();
            
            // Update current world player count
            $currentWorld = $currentSession->gameWorld;
            $currentWorld->updatePlayerCount(max(0, $currentWorld->current_players - 1));
        }

        // Create new session in target world
        $newSessionId = 'session_' . uniqid();
        $newSession = PlayerWorldSession::create([
            'player_id' => $player->id,
            'game_world_id' => $targetWorld->id,
            'session_id' => $newSessionId,
            'player_name' => $player->name,
            'player_state' => $request->player_state ?? [],
            'is_active' => true
        ]);

        // Update target world player count
        $targetWorld->updatePlayerCount($targetWorld->current_players + 1);

        // Transfer resources if specified
        $resourcesTransferred = [];
        if ($request->resources_to_transfer) {
            foreach ($request->resources_to_transfer as $resourceType => $amount) {
                if ($player->removeResource($resourceType, $amount)) {
                    $resourcesTransferred[$resourceType] = $amount;
                }
            }
        }

        // Record transfer
        $transfer = WorldTransfer::create([
            'player_id' => $player->id,
            'from_world_id' => $fromWorldId,
            'to_world_id' => $targetWorld->id,
            'transfer_type' => $request->transfer_type,
            'resources_transferred' => $resourcesTransferred,
            'player_state' => $request->player_state ?? [],
            'successful' => true
        ]);

        // Broadcast transfer completed event
        broadcast(new WorldTransferCompleted($transfer))->toOthers();

        return response()->json([
            'success' => true,
            'transfer' => $transfer,
            'new_session' => $newSession,
            'target_world' => $targetWorld,
            'resources_transferred' => $resourcesTransferred
        ]);
    }

    /**
     * Get available worlds for transfer
     */
    public function getAvailableWorlds(Request $request)
    {
        $worlds = GameWorld::where('status', 'online')
            ->where('current_players', '<', DB::raw('max_players'))
            ->where('last_heartbeat', '>', now()->subMinutes(5))
            ->get()
            ->map(function ($world) {
                return [
                    'id' => $world->id,
                    'name' => $world->name,
                    'server_id' => $world->server_id,
                    'current_players' => $world->current_players,
                    'max_players' => $world->max_players,
                    'available_capacity' => $world->getAvailableCapacity(),
                    'description' => $world->description
                ];
            });

        return response()->json($worlds);
    }

    /**
     * Update player resources
     */
    public function updatePlayerResources(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string',
            'resources' => 'required|array',
            'resources.*.type' => 'required|string',
            'resources.*.amount' => 'required|integer|min:0',
            'operation' => 'required|in:add,remove,set' // add, remove, or set
        ]);

        $player = Player::where('name', $request->player_name)->first();
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $results = [];

        foreach ($request->resources as $resource) {
            $type = $resource['type'];
            $amount = $resource['amount'];
            
            switch ($request->operation) {
                case 'add':
                    $player->addResource($type, $amount);
                    $results[$type] = ['success' => true, 'new_amount' => $player->getResource($type)];
                    break;
                    
                case 'remove':
                    $success = $player->removeResource($type, $amount);
                    $results[$type] = [
                        'success' => $success, 
                        'new_amount' => $player->getResource($type)
                    ];
                    break;
                    
                case 'set':
                    $player->resources()->updateOrCreate(
                        ['resource_type' => $type],
                        ['amount' => $amount, 'last_updated' => now()]
                    );
                    $results[$type] = ['success' => true, 'new_amount' => $amount];
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total_resources' => $player->getTotalResources()
        ]);
    }

    /**
     * Get player resources
     */
    public function getPlayerResources(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string'
        ]);

        $player = Player::where('name', $request->player_name)->first();
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        return response()->json([
            'player' => $player->name,
            'resources' => $player->getTotalResources(),
            'current_world' => $player->getCurrentWorld()?->name
        ]);
    }

    /**
     * Update player score and game state
     */
    public function updateScore(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string',
            'score' => 'required|numeric|min:0',
            'stage' => 'required|numeric|min:0',
            'bonus' => 'numeric|min:0',
            'game_data' => 'array',
            'server_id' => 'string'
        ]);

        $playerName = $request->player_name;

        // Get existing player or create new one
        $player = Player::where('name', $playerName)->first();
        
        if (!$player) {
            // Only create new player with defaults
            $player = Player::create([
                'name' => $playerName,
                'callsign' => $playerName,
                'last_game_at' => now(),
                'resources' => ['gold' => 0, 'water' => 0, 'kryptonite' => 0],
                'total_score' => 0
            ]);
        }

        // Update score without overwriting existing data
        $player->update([
            'total_score' => $request->score,
            'last_game_at' => now()
        ]);

        // Store score in database
        $score = Score::create([
            'self' => $playerName,
            'provider' => 'solarsys_master',
            'score' => $request->score,
            'stage' => $request->stage,
            'bonus' => $request->bonus ?? 0,
            'nft_earned' => $request->input('nft_earned', 'none'),
            'json' => json_encode($request->input('game_data', []))
        ]);

        // Update player last game time
        $player->updateLastGame();

        // Invalidate leaderboard cache
        Cache::forget($this->leaderboardCacheKey);

        // Broadcast score update to all connected clients
        broadcast(new ScoreUpdated($score))->toOthers();

        return response()->json([
            'success' => true,
            'score_id' => $score->id,
            'new_high_score' => $this->checkNewHighScore($request->score, $playerName)
        ]);
    }

    /**
     * Get leaderboard data
     */
    public function getLeaderboard(?Request $request = null)
    {
        $limit = $request ? $request->input('limit', 10) : 10;
        $timeframe = $request ? $request->input('timeframe', 'all') : 'all';

        return Cache::remember("{$this->leaderboardCacheKey}_{$timeframe}_{$limit}", 
            $this->leaderboardCacheDuration, 
            function () use ($limit, $timeframe) {
                return $this->buildLeaderboard($limit, $timeframe);
            }
        );
    }

    /**
     * Get player statistics
     */
    public function getPlayerStats(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string'
        ]);

        $player = Player::where('name', $request->player_name)->first();
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $stats = $this->buildPlayerStats($player);
        
        return response()->json($stats);
    }

    /**
     * Create world event
     */
    public function createWorldEvent(Request $request)
    {
        $request->validate([
            'world_id' => 'required|exists:game_worlds,id',
            'event_type' => 'required|string|max:100',
            'event_name' => 'required|string|max:255',
            'event_data' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now'
        ]);

        $event = WorldEvent::create([
            'game_world_id' => $request->world_id,
            'event_type' => $request->event_type,
            'event_name' => $request->event_name,
            'event_data' => $request->event_data,
            'expires_at' => $request->expires_at
        ]);

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    /**
     * Get world events
     */
    public function getWorldEvents(Request $request)
    {
        $worldId = $request->input('world_id');
        $eventType = $request->input('event_type');
        $activeOnly = $request->input('active_only', true);

        $query = WorldEvent::query();

        if ($worldId) {
            $query->where('game_world_id', $worldId);
        }

        if ($eventType) {
            $query->where('event_type', $eventType);
        }

        if ($activeOnly) {
            $query->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                  });
        }

        $events = $query->orderBy('event_time', 'desc')->get();

        return response()->json($events);
    }

    /**
     * Get server health and status
     */
    public function getServerHealth()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'uptime' => now()->diffInSeconds($this->gameState['server_start_time']),
            'memory_usage' => memory_get_usage(true),
            'active_connections' => $this->getActivePlayerCount(),
            'database_status' => $this->checkDatabaseConnection(),
            'worlds_online' => GameWorld::where('status', 'online')->count(),
            'total_worlds' => GameWorld::count()
        ]);
    }

    /**
     * Unregister a game world
     */
    public function unregisterWorld(Request $request)
    {
        $request->validate([
            'server_id' => 'required|string|exists:game_worlds,server_id'
        ]);

        $world = GameWorld::where('server_id', $request->server_id)->first();
        
        // Disconnect all active players
        $world->activeSessions()->update([
            'is_active' => false,
            'disconnected_at' => now()
        ]);

        // Mark world as offline
        $world->update([
            'status' => 'offline',
            'current_players' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game world unregistered successfully'
        ]);
    }

    /**
     * Private helper methods
     */
    private function getActivePlayerCount()
    {
        return PlayerWorldSession::where('is_active', true)->count();
    }

    private function getTotalGamesPlayed()
    {
        return Score::count();
    }

    private function buildPlayerStats(Player $player)
    {
        $scores = $player->scores;
        $worldSessions = $player->worldSessions;
        $transfers = $player->worldTransfers;
        
        return [
            'player_name' => $player->name,
            'callsign' => $player->callsign,
            'total_games' => $scores->count(),
            'high_score' => $scores->max('score') ?? 0,
            'total_score' => $scores->sum('score'),
            'average_score' => $scores->avg('score') ?? 0,
            'best_stage' => $scores->max('stage') ?? 0,
            'last_played' => $scores->latest()->first()?->created_at,
            'rank' => $this->getPlayerRank($player->name),
            'resources' => $player->getTotalResources(),
            'current_world' => $player->getCurrentWorld()?->name,
            'is_online' => $player->isOnline(),
            'worlds_visited' => $worldSessions->pluck('gameWorld.name')->unique()->count(),
            'total_transfers' => $transfers->count(),
            'total_playtime' => $worldSessions->sum(DB::raw('TIMESTAMPDIFF(MINUTE, connected_at, COALESCE(disconnected_at, NOW()))')),
            'last_login' => $player->last_login,
            'last_game_at' => $player->last_game_at
        ];
    }

    private function buildLeaderboard($limit, $timeframe)
    {
        $query = Score::select('self', DB::raw('MAX(score) as high_score'), DB::raw('COUNT(*) as games_played'))
            ->groupBy('self');

        // Apply timeframe filter
        switch ($timeframe) {
            case 'daily':
                $query->whereDate('created_at', today());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('created_at', now()->month);
                break;
        }

        return $query->orderBy('high_score', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'player_name' => $item->self,
                    'high_score' => $item->high_score,
                    'games_played' => $item->games_played
                ];
            });
    }

    private function getGlobalStats()
    {
        return Cache::remember('global_game_stats', 600, function () {
            return [
                'total_players' => Player::count(),
                'active_players' => PlayerWorldSession::where('is_active', true)->count(),
                'total_games' => Score::count(),
                'average_score' => Score::avg('score'),
                'highest_score' => Score::max('score'),
                'most_active_player' => Score::select('self', DB::raw('COUNT(*) as games'))
                    ->groupBy('self')
                    ->orderBy('games', 'desc')
                    ->first(),
                'worlds_online' => GameWorld::where('status', 'online')->count(),
                'total_worlds' => GameWorld::count()
            ];
        });
    }

    private function getPlayerRank($playerName)
    {
        $rank = Score::select('self', DB::raw('MAX(score) as high_score'))
            ->groupBy('self')
            ->orderBy('high_score', 'desc')
            ->pluck('self')
            ->search($playerName);

        return $rank !== false ? $rank + 1 : null;
    }

    private function checkNewHighScore($score, $playerName)
    {
        $currentHigh = Score::max('score') ?? 0;
        return $score > $currentHigh;
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'disconnected';
        }
    }

    // New method to check server health
    public function checkServersHealth()
    {
        $threshold = now()->subSeconds(
            self::HEARTBEAT_INTERVAL + self::HEARTBEAT_GRACE_PERIOD
        );

        GameWorld::where('status', '!=', GameWorld::STATUS_SHUTDOWN)
            ->where(function ($query) use ($threshold) {
                $query->where('last_heartbeat', '<', $threshold)
                      ->orWhereNull('last_heartbeat');
            })
            ->each(function ($world) {
                $world->recordHeartbeatFailure();
            });
    }

    public function getPlayerState(string $playerId)
    {
        // Get player by name (which is the unique identifier)
        $player = Player::where('name', $playerId)->first();
        
        if (!$player) {
            return response()->json([
                'resources' => ['gold' => 0, 'water' => 0, 'kryptonite' => 0],
                'score' => 0,
                'rank' => null
            ]);
        }

        return response()->json([
            'resources' => $player->resources ?? ['gold' => 0, 'water' => 0, 'kryptonite' => 0],
            'score' => $player->total_score ?? 0,
            'rank' => null
        ]);
    }

    public function updatePlayerState(Request $request, string $playerId)
    {
        $data = $request->all();
        
        // Check if create_only flag is set first
        if (isset($data['create_only']) && $data['create_only'] === true) {
            // Only create if doesn't exist, never update
            $player = Player::firstOrCreate([
                'name' => $playerId
            ], [
                'email' => null,
                'callsign' => null,
                'resources' => ['gold' => 0, 'water' => 0, 'kryptonite' => 0],
                'total_score' => 0
            ]);
            
            return response()->json(['success' => true]);
        }

        // Normal update for collection events
        $player = Player::where('name', $playerId)->first();
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        if (isset($data['resources']) && isset($data['score'])) {
            $player->update([
                'resources' => $data['resources'],
                'total_score' => $data['score']
            ]);
        }

        return response()->json(['success' => true]);
    }
} 