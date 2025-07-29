<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'callsign',
        'avatar',
        'preferences',
        'resources',
        'total_score',
        'last_login',
        'last_game_at',
        'is_active'
    ];

    protected $casts = [
        'preferences' => 'array',
        'resources' => 'array',
        'last_login' => 'datetime',
        'last_game_at' => 'datetime'
    ];

    /**
     * Get all world sessions for this player
     */
    public function worldSessions(): HasMany
    {
        return $this->hasMany(PlayerWorldSession::class);
    }

    /**
     * Get current active session
     */
    public function activeSession(): HasOne
    {
        return $this->hasOne(PlayerWorldSession::class)->where('is_active', true);
    }

    /**
     * Get all world transfers for this player
     */
    public function worldTransfers(): HasMany
    {
        return $this->hasMany(WorldTransfer::class);
    }

    /**
     * Get all scores for this player
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'self', 'name');
    }

    /**
     * Get player's total resources across all types
     */
    public function getTotalResources(): array
    {
        return $this->resources()
            ->pluck('amount', 'resource_type')
            ->toArray();
    }

    /**
     * Get player's resource amount for specific type
     */
    public function getResource(string $type): int
    {
        return $this->resources()
            ->where('resource_type', $type)
            ->value('amount') ?? 0;
    }

    /**
     * Add resources to player
     */
    public function addResource(string $type, int $amount): void
    {
        $resource = $this->resources()->firstOrCreate(
            ['resource_type' => $type],
            ['amount' => 0]
        );
        
        $resource->add($amount);
    }

    /**
     * Remove resources from player (returns false if insufficient)
     */
    public function removeResource(string $type, int $amount): bool
    {
        $resource = $this->resources()->where('resource_type', $type)->first();
        
        if (!$resource) {
            return false;
        }
        
        return $resource->remove($amount);
    }

    /**
     * Check if player has sufficient resources
     */
    public function hasResource(string $type, int $amount): bool
    {
        return $this->getResource($type) >= $amount;
    }

    /**
     * Get current world session
     */
    public function getCurrentWorld(): ?GameWorld
    {
        return $this->activeSession?->gameWorld;
    }

    /**
     * Check if player is online in any world
     */
    public function isOnline(): bool
    {
        return $this->activeSession()->exists();
    }

    /**
     * Update last login time
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login' => now()]);
    }

    /**
     * Update last game time
     */
    public function updateLastGame(): void
    {
        $this->update(['last_game_at' => now()]);
    }

    /**
     * Get player statistics
     */
    public function getStats(): array
    {
        $scores = $this->scores;
        $worldSessions = $this->worldSessions;
        $transfers = $this->worldTransfers;
        
        return [
            'total_games' => $scores->count(),
            'high_score' => $scores->max('score') ?? 0,
            'total_score' => $scores->sum('score'),
            'average_score' => $scores->avg('score') ?? 0,
            'best_stage' => $scores->max('stage') ?? 0,
            'last_played' => $scores->latest()->first()?->created_at,
            'worlds_visited' => $worldSessions->pluck('gameWorld.name')->unique()->count(),
            'total_transfers' => $transfers->count(),
            'total_playtime' => $worldSessions->sum(DB::raw('TIMESTAMPDIFF(MINUTE, connected_at, COALESCE(disconnected_at, NOW()))'))
        ];
    }
} 