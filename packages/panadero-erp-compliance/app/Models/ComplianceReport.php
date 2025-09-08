<?php

namespace Panadero\ErpCompliance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_number',
        'title',
        'type',
        'category',
        'description',
        'status',
        'report_date',
        'period_start',
        'period_end',
        'data',
        'metrics',
        'metadata',
        'file_path',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'report_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'approved_at' => 'datetime',
        'data' => 'array',
        'metrics' => 'array',
        'metadata' => 'array'
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
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->where('period_start', '>=', $startDate)
                    ->where('period_end', '<=', $endDate);
    }

    // Accessors & Mutators
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published';
    }

    public function getHasFileAttribute()
    {
        return !empty($this->file_path) && file_exists(storage_path('app/' . $this->file_path));
    }

    public function getPeriodDurationAttribute()
    {
        if ($this->period_start && $this->period_end) {
            return $this->period_start->diffInDays($this->period_end);
        }
        return null;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'published' => 'green',
            'approved' => 'blue',
            'generated' => 'yellow',
            'draft' => 'gray',
            default => 'gray'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'regulatory' => 'red',
            'internal' => 'blue',
            'audit' => 'orange',
            'risk' => 'purple',
            'policy' => 'green',
            default => 'gray'
        };
    }
}
