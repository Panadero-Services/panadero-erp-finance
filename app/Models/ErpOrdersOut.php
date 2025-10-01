<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpOrdersOut extends Model
{
    use HasFactory;

    protected $table = 'erp_orders_out';

    protected $fillable = [
        'ordernr',
        'customer_id',
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

    public function customer()
    {
        return $this->belongsTo(ErpCustomer::class, 'customer_id');
    }

    public function site()
    {
        return $this->belongsTo(ErpSite::class, 'site_id');
    }

    public function status()
    {
        return $this->belongsTo(ErpStatus::class, 'status_id');
    }

    public function orderOutLines()
    {
        return $this->hasMany(ErpOrderOutLine::class, 'order_out_id');
    }
}

