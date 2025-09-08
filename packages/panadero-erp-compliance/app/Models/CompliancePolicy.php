<?php

namespace Panadero\ErpCompliance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompliancePolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'category',
        'description',
        'content',
        'status',
        'version',
        'effective_date',
        'expiry_date',
        'metadata',
        'requirements',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'metadata' => 'array',
        'requirements' => 'array'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('status', 'active');
    }

    // Accessors & Mutators
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'draft' => 'yellow',
            'inactive' => 'gray',
            'archived' => 'red',
            default => 'gray'
        };
    }
}
