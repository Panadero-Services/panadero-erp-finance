<?php

namespace Panadero\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period',
        'review_date',
        'period_start',
        'period_end',
        'goals',
        'achievements',
        'skills_assessment',
        'overall_rating',
        'strengths',
        'areas_for_improvement',
        'manager_comments',
        'employee_comments',
        'development_plan',
        'status',
        'employee_acknowledged_at',
    ];

    protected $casts = [
        'goals' => 'array',
        'achievements' => 'array',
        'skills_assessment' => 'array',
        'development_plan' => 'array',
        'review_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'employee_acknowledged_at' => 'datetime',
    ];

    /**
     * Get the employee this review is for
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the reviewer
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewer_id');
    }

    /**
     * Scope for reviews by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for completed reviews
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for reviews by rating
     */
    public function scopeByRating($query, $minRating)
    {
        return $query->where('overall_rating', '>=', $minRating);
    }

    /**
     * Scope for reviews in a specific period
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('review_date', [$startDate, $endDate]);
    }

    /**
     * Get the rating as a text description
     */
    public function getRatingDescriptionAttribute(): string
    {
        return match ($this->overall_rating) {
            1 => 'Needs Improvement',
            2 => 'Below Expectations',
            3 => 'Meets Expectations',
            4 => 'Exceeds Expectations',
            5 => 'Outstanding',
            default => 'Not Rated',
        };
    }

    /**
     * Check if review is acknowledged by employee
     */
    public function isAcknowledged(): bool
    {
        return !is_null($this->employee_acknowledged_at);
    }

    /**
     * Mark review as acknowledged by employee
     */
    public function markAsAcknowledged(): void
    {
        $this->update([
            'employee_acknowledged_at' => now(),
        ]);
    }

    /**
     * Get the average skills rating
     */
    public function getAverageSkillsRatingAttribute(): ?float
    {
        if (!$this->skills_assessment || !is_array($this->skills_assessment)) {
            return null;
        }

        $ratings = array_values($this->skills_assessment);
        return count($ratings) > 0 ? array_sum($ratings) / count($ratings) : null;
    }
}
