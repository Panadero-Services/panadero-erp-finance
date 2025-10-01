<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpOrdersIn extends Model
{
    use HasFactory;

    protected $table = 'erp_orders_in';

    protected $fillable = [
        'ordernr',
        'supplier_id',
        'site_id',
        'status_id',
        'eventHandlers',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'eventHandlers' => 'array',
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

    public function supplier()
    {
        return $this->belongsTo(ErpSupplier::class, 'supplier_id');
    }

    public function site()
    {
        return $this->belongsTo(ErpSite::class, 'site_id');
    }

    public function status()
    {
        return $this->belongsTo(ErpStatus::class, 'status_id');
    }

    public function orderInLines()
    {
        return $this->hasMany(ErpOrderInLine::class, 'order_in_id');
    }
}

