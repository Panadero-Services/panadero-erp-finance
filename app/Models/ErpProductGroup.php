<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpProductGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'erp_product_type_id',  // Changed from 'product_type_id'
        'parent_id',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the product type this group belongs to
     */
    public function productType(): BelongsTo
    {
        return $this->belongsTo(ErpProductType::class, 'erp_product_type_id');  // Changed from 'product_type_id'
    }

    /**
     * Get the parent product group
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ErpProductGroup::class, 'parent_id');
    }

    /**
     * Get the child product groups
     */
    public function children(): HasMany
    {
        return $this->hasMany(ErpProductGroup::class, 'parent_id');
    }

    /**
     * Get all products in this group
     */
    public function products(): HasMany
    {
        return $this->hasMany(ErpProduct::class, 'product_group_id');
    }

    /**
     * Get all descendant groups (children, grandchildren, etc.)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestor groups (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }
        
        return $ancestors;
    }

    /**
     * Get the full path of the group (e.g., "Electronics > Computers > Laptops")
     */
    public function getFullPathAttribute(): string
    {
        $path = collect($this->ancestors())->pluck('name')->reverse()->push($this->name);
        return $path->implode(' > ');
    }

    /**
     * Scope for active groups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root groups (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for groups with children
     */
    public function scopeWithChildren($query)
    {
        return $query->whereHas('children');
    }
}