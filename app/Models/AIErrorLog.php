<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIErrorLog extends Model
{
    protected $fillable = [
        'error_type',
        'error_data',
        'model',
        'response_time_ms',
        'is_timeout'
    ];

    protected $casts = [
        'error_data' => 'array',
        'is_timeout' => 'boolean',
        'response_time_ms' => 'integer'
    ];
}
