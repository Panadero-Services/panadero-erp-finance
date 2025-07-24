<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorldEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_world_id',
        'event_type',
        'event_name',
        'event_data',
        'event_time',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'event_time' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Get the game world for this event
     */
    public function gameWorld(): BelongsTo
    {
        return $this->belongsTo(GameWorld::class);
    }

    /**
     * Check if event is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Deactivate expired events
     */
    public static function deactivateExpired(): void
    {
        static::where('expires_at', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
} 