<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpSite extends Model
{
    use HasFactory;

    protected $table = 'erp_sites';

    protected $fillable = [
        'name',
        'identifier',
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

    public function storages()
    {
        return $this->hasMany(ErpStorage::class, 'site_id');
    }

    public function ordersIn()
    {
        return $this->hasMany(ErpOrdersIn::class, 'site_id');
    }

    public function ordersOut()
    {
        return $this->hasMany(ErpOrdersOut::class, 'site_id');
    }
}

