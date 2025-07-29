<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameWorld extends Model
{
    use HasFactory;

    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_UNREACHABLE = 'unreachable';  // New state for unresponsive servers
    const STATUS_SHUTDOWN = 'shutdown';         // New state for proper shutdowns

    protected $fillable = [
        'name',
        'server_url',
        'server_id',
        'description',
        'status',
        'max_players',
        'current_players',
        'world_config',
        'last_heartbeat',
        'state_changes',      // JSON field to track state history
        'last_state_change',  // When state last changed
        'consecutive_fails',  // Count failed heartbeats
    ];

    protected $casts = [
        'world_config' => 'array',
        'last_heartbeat' => 'datetime',
        'state_changes' => 'array',
        'last_state_change' => 'datetime',
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
        return $this->status === self::STATUS_ONLINE && 
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

    public function updateServerState(string $newStatus, ?string $reason = null): void
    {
        $oldStatus = $this->status;
        if ($oldStatus !== $newStatus) {
            // Track state change
            $this->state_changes = array_merge($this->state_changes ?? [], [[
                'from' => $oldStatus,
                'to' => $newStatus,
                'timestamp' => now(),
                'reason' => $reason
            ]]);
            
            $this->status = $newStatus;
            $this->last_state_change = now();
            
            // Reset fails counter if coming back online
            if ($newStatus === self::STATUS_ONLINE) {
                $this->consecutive_fails = 0;
            }
            
            $this->save();
        }
    }

    public function recordHeartbeatFailure(): void
    {
        $this->consecutive_fails++;
        
        // After 3 failed heartbeats, mark as unreachable
        if ($this->consecutive_fails >= 3) {
            $this->updateServerState(
                self::STATUS_UNREACHABLE, 
                'Failed heartbeat threshold exceeded'
            );
        }
        
        $this->save();
    }
} 