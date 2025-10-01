<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpSample extends Model
{
    use HasFactory;

    protected $table = 'erp_samples';

    protected $fillable = [
        'name',
        'description',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'json' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }
}

