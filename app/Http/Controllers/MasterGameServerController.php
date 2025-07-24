<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterGameServerController extends Controller
{
    protected $gameState;
    protected $leaderboardCacheKey = 'game_leaderboard';
    protected $leaderboardCacheDuration = 300; // 5 minutes

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
     * Get current game state and statistics
     */
    public function getGameState(Request $request)
    {
        $userId = $request->user()?->id;
        
        $gameState = [
            'server' => [
                'status' => 'online',
                'uptime' => now()->diffInSeconds($this->gameState['server_start_time']),
                'active_players' => $this->getActivePlayerCount(),
                'total_games_played' => $this->getTotalGamesPlayed(),
                'current_round' => $this->gameState['current_round']
            ],
            'player' => $userId ? $this->buildPlayerStats($userId) : null,
            'leaderboard' => $this->getLeaderboard(),
            'global_stats' => $this->getGlobalStats()
        ];

        return response()->json($gameState);
    }

    /**
     * Update player score and game state
     */
    public function updateScore(Request $request)
    {
        $request->validate([
            'score' => 'required|numeric|min:0',
            'stage' => 'required|numeric|min:0',
            'bonus' => 'numeric|min:0',
            'game_data' => 'array',
            'server_id' => 'string'
        ]);

        $userId = $request->user()?->id;
        $playerName = $request->user()?->name ?? $request->input('player_name', 'Anonymous');

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

        // Update player stats
        if ($userId) {
            $this->updatePlayerStats($userId, $request->score, $request->stage);
        }

        // Invalidate leaderboard cache
        Cache::forget($this->leaderboardCacheKey);

        // Broadcast score update to all connected clients
        broadcast(new \App\Events\ScoreUpdated($score))->toOthers();

        return response()->json([
            'success' => true,
            'score_id' => $score->id,
            'new_high_score' => $this->checkNewHighScore($request->score, $playerName)
        ]);
    }

    /**
     * Get leaderboard data
     */
    public function getLeaderboard(Request $request)
    {
        $limit = $request->input('limit', 10);
        $timeframe = $request->input('timeframe', 'all'); // all, daily, weekly, monthly

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
        $userId = $request->user()?->id;
        
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $stats = $this->buildPlayerStats($userId);
        
        return response()->json($stats);
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
            'database_status' => $this->checkDatabaseConnection()
        ]);
    }

    /**
     * Private helper methods
     */
    private function getActivePlayerCount()
    {
        // This would integrate with your WebSocket server
        // For now, return cached value or estimate
        return Cache::get('active_players_count', 0);
    }

    private function getTotalGamesPlayed()
    {
        return Score::count();
    }

    private function buildPlayerStats($userId)
    {
        $user = User::find($userId);
        if (!$user) return null;

        $scores = Score::where('self', $user->name)->get();
        
        return [
            'player_name' => $user->name,
            'total_games' => $scores->count(),
            'high_score' => $scores->max('score') ?? 0,
            'total_score' => $scores->sum('score'),
            'average_score' => $scores->avg('score') ?? 0,
            'best_stage' => $scores->max('stage') ?? 0,
            'last_played' => $scores->latest()->first()?->created_at,
            'rank' => $this->getPlayerRank($user->name)
        ];
    }

    private function updatePlayerStats($userId, $score, $stage)
    {
        $user = User::find($userId);
        if (!$user) return;

        // Update user's game statistics
        $user->update([
            'last_game_score' => $score,
            'last_game_stage' => $stage,
            'last_game_at' => now()
        ]);
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
                'total_players' => Score::distinct('self')->count(),
                'total_games' => Score::count(),
                'average_score' => Score::avg('score'),
                'highest_score' => Score::max('score'),
                'most_active_player' => Score::select('self', DB::raw('COUNT(*) as games'))
                    ->groupBy('self')
                    ->orderBy('games', 'desc')
                    ->first()
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
} 