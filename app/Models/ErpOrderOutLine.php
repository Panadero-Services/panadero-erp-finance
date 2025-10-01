<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpOrderOutLine extends Model
{
    use HasFactory;

    protected $table = 'erp_order_out_lines';

    protected $fillable = [
        'order_out_id',
        'product_id',
        'qty',
        'site_id',
        'status_id',
        'eventHandlers',
        'stockHandler',
        'sample_id',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'eventHandlers' => 'array',
        'stockHandler' => 'array',
        'json' => 'array',
        'qty' => 'decimal:4'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function orderOut()
    {
        return $this->belongsTo(ErpOrdersOut::class, 'order_out_id');
    }

    public function product()
    {
        return $this->belongsTo(ErpProduct::class, 'product_id');
    }

    public function site()
    {
        return $this->belongsTo(ErpSite::class, 'site_id');
    }

    public function status()
    {
        return $this->belongsTo(ErpStatus::class, 'status_id');
    }
}

