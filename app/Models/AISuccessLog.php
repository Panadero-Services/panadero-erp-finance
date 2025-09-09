<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AISuccessLog extends Model
{
    protected $fillable = [
        'success_data',
        'model',
        'response_time_ms',
        'tokens_generated'
    ];

    protected $casts = [
        'success_data' => 'array',
        'response_time_ms' => 'integer',
        'tokens_generated' => 'integer'
    ];
}
