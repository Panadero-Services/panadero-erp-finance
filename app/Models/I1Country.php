<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class I1Country extends Model
{
    use HasFactory;

    protected $table = 'i1_countries';

    protected $fillable = [
        'name',
        'identifier',
        'min_temp',
        'max_temp',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'min_temp' => 'decimal:1',
        'max_temp' => 'decimal:1',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
} 