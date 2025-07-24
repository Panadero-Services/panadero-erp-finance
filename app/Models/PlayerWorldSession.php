<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerWorldSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'game_world_id',
        'session_id',
        'player_name',
        'player_state',
        'connected_at',
        'disconnected_at',
        'is_active'
    ];

    protected $casts = [
        'player_state' => 'array',
        'connected_at' => 'datetime',
        'disconnected_at' => 'datetime'
    ];

    /**
     * Get the player for this session
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the game world for this session
     */
    public function gameWorld(): BelongsTo
    {
        return $this->belongsTo(GameWorld::class);
    }

    /**
     * Disconnect the session
     */
    public function disconnect(): void
    {
        $this->update([
            'disconnected_at' => now(),
            'is_active' => false
        ]);
    }

    /**
     * Update player state
     */
    public function updateState(array $state): void
    {
        $this->update(['player_state' => $state]);
    }
} 