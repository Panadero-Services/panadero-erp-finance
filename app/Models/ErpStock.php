<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpStock extends Model
{
    use HasFactory;

    protected $table = 'erp_stocks';

    protected $fillable = [
        'storage_id',
        'product_id',
        'qty',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
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

    public function storage()
    {
        return $this->belongsTo(ErpStorage::class, 'storage_id');
    }

    public function product()
    {
        return $this->belongsTo(ErpProduct::class, 'product_id');
    }
}

