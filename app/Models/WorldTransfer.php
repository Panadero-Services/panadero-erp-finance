<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorldTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'from_world_id',
        'to_world_id',
        'transfer_type',
        'resources_transferred',
        'player_state',
        'transfer_time',
        'successful',
        'notes'
    ];

    protected $casts = [
        'resources_transferred' => 'array',
        'player_state' => 'array',
        'transfer_time' => 'datetime'
    ];

    /**
     * Get the player for this transfer
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the source world
     */
    public function fromWorld(): BelongsTo
    {
        return $this->belongsTo(GameWorld::class, 'from_world_id');
    }

    /**
     * Get the destination world
     */
    public function toWorld(): BelongsTo
    {
        return $this->belongsTo(GameWorld::class, 'to_world_id');
    }
} 