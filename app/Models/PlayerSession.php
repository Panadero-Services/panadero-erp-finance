<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerSession extends Model
{
    protected $fillable = [
        'player_id',
        'server_id',
        'status',
        'data',
        'last_heartbeat'
    ];

    protected $casts = [
        'data' => 'array',
        'last_heartbeat' => 'datetime'
    ];
}