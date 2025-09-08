<?php

namespace Panadero\ErpCompliance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceRisk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'risk_id',
        'title',
        'description',
        'category',
        'type',
        'status',
        'probability',
        'impact',
        'risk_level',
        'mitigation_plan',
        'controls',
        'identified_date',
        'target_resolution_date',
        'actual_resolution_date',
        'metadata',
        'owner_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'identified_date' => 'date',
        'target_resolution_date' => 'date',
        'actual_resolution_date' => 'date',
        'metadata' => 'array'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_resolution_date', '<', now())
                    ->whereIn('status', ['identified', 'assessed']);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    // Accessors & Mutators
    public function getIsOverdueAttribute()
    {
        return $this->target_resolution_date && $this->target_resolution_date->isPast() && 
               in_array($this->status, ['identified', 'assessed']);
    }

    public function getIsResolvedAttribute()
    {
        return $this->status === 'closed' || $this->actual_resolution_date !== null;
    }

    public function getDaysToResolutionAttribute()
    {
        if ($this->target_resolution_date) {
            return now()->diffInDays($this->target_resolution_date, false);
        }
        return null;
    }

    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'closed' => 'green',
            'mitigated' => 'blue',
            'assessed' => 'yellow',
            'identified' => 'orange',
            default => 'gray'
        };
    }

    public function getProbabilityColorAttribute()
    {
        return match($this->probability) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'very_high' => 'red',
            default => 'gray'
        };
    }

    public function getImpactColorAttribute()
    {
        return match($this->impact) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }
}
