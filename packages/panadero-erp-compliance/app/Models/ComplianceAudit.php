<?php

namespace Panadero\ErpCompliance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audit_number',
        'title',
        'type',
        'scope',
        'description',
        'status',
        'start_date',
        'end_date',
        'due_date',
        'findings',
        'recommendations',
        'risk_level',
        'metadata',
        'auditor_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'due_date' => 'date',
        'findings' => 'array',
        'recommendations' => 'array',
        'metadata' => 'array'
    ];

    // Relationships
    public function auditor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'auditor_id');
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
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Accessors & Mutators
    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && 
               in_array($this->status, ['planned', 'in_progress']);
    }

    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
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
            'completed' => 'green',
            'in_progress' => 'blue',
            'planned' => 'yellow',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getTotalFindingsAttribute()
    {
        if (!$this->findings) return 0;
        
        return array_sum($this->findings);
    }
}
