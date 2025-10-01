<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpBrand extends Model
{
    use HasFactory;

    protected $table = 'erp_brands';

    protected $fillable = [
        'name',
        'identifier',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'json' => 'array',
        'is_active' => 'boolean',
        'is_locked' => 'boolean'
    ];

    /**
     * Get the products for this brand.
     */
    public function products()
    {
        return $this->hasMany(ErpProduct::class, 'erp_brand_id');
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include unlocked brands.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope a query to only include active and unlocked brands.
     */
    public function scopeAvailable($query)
    {
        return $query->active()->unlocked();
    }
}