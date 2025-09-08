<?php

namespace Panadero\ErpCompliance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceRCA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compliance_rca';

    protected $fillable = [
        'rca_id',
        'title',
        'description',
        'incident_type',
        'severity',
        'status',
        'problem_statement',
        'immediate_actions',
        'root_causes',
        'contributing_factors',
        'corrective_actions',
        'preventive_measures',
        'incident_date',
        'discovery_date',
        'target_resolution_date',
        'actual_resolution_date',
        'metadata',
        'ai_analysis',
        'assigned_to',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'incident_date' => 'date',
        'discovery_date' => 'date',
        'target_resolution_date' => 'date',
        'actual_resolution_date' => 'date',
        'root_causes' => 'array',
        'contributing_factors' => 'array',
        'corrective_actions' => 'array',
        'preventive_measures' => 'array',
        'metadata' => 'array',
        'ai_analysis' => 'array'
    ];

    // Relationships
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
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
    public function scopeByIncidentType($query, $type)
    {
        return $query->where('incident_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHighSeverity($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_resolution_date', '<', now())
                    ->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWithAIAnalysis($query)
    {
        return $query->whereNotNull('ai_analysis');
    }

    // Accessors & Mutators
    public function getIsOverdueAttribute()
    {
        return $this->target_resolution_date && $this->target_resolution_date->isPast() && 
               in_array($this->status, ['open', 'in_progress']);
    }

    public function getIsResolvedAttribute()
    {
        return $this->status === 'completed' || $this->actual_resolution_date !== null;
    }

    public function getDaysToResolutionAttribute()
    {
        if ($this->target_resolution_date) {
            return now()->diffInDays($this->target_resolution_date, false);
        }
        return null;
    }

    public function getResolutionTimeAttribute()
    {
        if ($this->incident_date && $this->actual_resolution_date) {
            return $this->incident_date->diffInDays($this->actual_resolution_date);
        }
        return null;
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
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
            'open' => 'yellow',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    public function getIncidentTypeColorAttribute()
    {
        return match($this->incident_type) {
            'compliance_violation' => 'red',
            'audit_finding' => 'orange',
            'risk_event' => 'purple',
            'policy_breach' => 'yellow',
            default => 'gray'
        };
    }

    public function getAIAnalysisScoreAttribute()
    {
        return $this->ai_analysis['risk_score'] ?? null;
    }

    public function getAIConfidenceScoreAttribute()
    {
        return $this->ai_analysis['confidence_score'] ?? null;
    }

    public function getAIPredictedLikelihoodAttribute()
    {
        return $this->ai_analysis['predicted_likelihood'] ?? null;
    }
}
