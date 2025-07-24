<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameWorld extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'server_url',
        'server_id',
        'description',
        'status',
        'max_players',
        'current_players',
        'world_config',
        'last_heartbeat'
    ];

    protected $casts = [
        'world_config' => 'array',
        'last_heartbeat' => 'datetime'
    ];

    /**
     * Get all active player sessions in this world
     */
    public function activeSessions(): HasMany
    {
        return $this->hasMany(PlayerWorldSession::class)->where('is_active', true);
    }

    /**
     * Get all player sessions in this world
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(PlayerWorldSession::class);
    }

    /**
     * Get world events
     */
    public function events(): HasMany
    {
        return $this->hasMany(WorldEvent::class);
    }

    /**
     * Get active events
     */
    public function activeEvents(): HasMany
    {
        return $this->hasMany(WorldEvent::class)->where('is_active', true);
    }

    /**
     * Check if world is online
     */
    public function isOnline(): bool
    {
        return $this->status === 'online' && 
               $this->last_heartbeat && 
               $this->last_heartbeat->diffInMinutes(now()) < 5;
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacity(): int
    {
        return max(0, $this->max_players - $this->current_players);
    }

    /**
     * Update player count
     */
    public function updatePlayerCount(int $count): void
    {
        $this->update([
            'current_players' => max(0, min($count, $this->max_players)),
            'last_heartbeat' => now()
        ]);
    }
} 