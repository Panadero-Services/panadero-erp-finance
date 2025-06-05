<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'provider', 'processor', 'executor', 'status', 'last_run_at'
    ];

    protected $casts = [
        'provider' => 'array',
        'processor' => 'array',
        'executor' => 'array',
        'last_run_at' => 'datetime'
    ];
}