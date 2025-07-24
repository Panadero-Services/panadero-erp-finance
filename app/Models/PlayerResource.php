<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'resource_type',
        'amount',
        'last_updated'
    ];

    protected $casts = [
        'last_updated' => 'datetime'
    ];

    /**
     * Get the player who owns this resource
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Add resources
     */
    public function add(int $amount): void
    {
        $this->update([
            'amount' => $this->amount + $amount,
            'last_updated' => now()
        ]);
    }

    /**
     * Remove resources (returns false if insufficient)
     */
    public function remove(int $amount): bool
    {
        if ($this->amount >= $amount) {
            $this->update([
                'amount' => $this->amount - $amount,
                'last_updated' => now()
            ]);
            return true;
        }
        return false;
    }

    /**
     * Set resource amount
     */
    public function set(int $amount): void
    {
        $this->update([
            'amount' => max(0, $amount),
            'last_updated' => now()
        ]);
    }
} 