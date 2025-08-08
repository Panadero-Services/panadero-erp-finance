<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerSession extends Model
{
    protected $fillable = [
        'player_id',
        'world_id',
        'status',
        'is_active',
        'last_seen_at',
        'disconnected_at',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
        'disconnected_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Possible statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_TRANSFERRING = 'transferring';
    const STATUS_RECOVERY_FAILED = 'recovery_failed';
    const STATUS_DISCONNECTED = 'disconnected';

    public function isTransferring()
    {
        return $this->status === self::STATUS_TRANSFERRING;
    }

    public function markAsRecoveryFailed()
    {
        $this->update([
            'status' => self::STATUS_RECOVERY_FAILED,
            'is_active' => false,
            'disconnected_at' => now()
        ]);
    }
}