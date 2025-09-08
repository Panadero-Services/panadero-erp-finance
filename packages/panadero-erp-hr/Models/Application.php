<?php

namespace Panadero\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cover_letter',
        'resume_path',
        'portfolio_url',
        'linkedin_url',
        'experience',
        'education',
        'skills',
        'certifications',
        'status',
        'ai_score',
        'ai_analysis',
        'ai_recommendations',
        'interview_scores',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'experience' => 'array',
        'education' => 'array',
        'skills' => 'array',
        'certifications' => 'array',
        'interview_scores' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the vacancy this application is for
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    /**
     * Get the user who reviewed this application
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    /**
     * Get the applicant's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Scope for applications by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'screening']);
    }

    /**
     * Scope for applications with AI scores
     */
    public function scopeWithAIScores($query)
    {
        return $query->whereNotNull('ai_score');
    }

    /**
     * Scope for high-scoring applications
     */
    public function scopeHighScoring($query, $minScore = 80)
    {
        return $query->where('ai_score', '>=', $minScore);
    }

    /**
     * Get the AI score as a percentage
     */
    public function getAIScorePercentageAttribute(): ?string
    {
        return $this->ai_score ? $this->ai_score . '%' : null;
    }

    /**
     * Check if application has been reviewed
     */
    public function isReviewed(): bool
    {
        return !is_null($this->reviewed_at);
    }

    /**
     * Mark application as reviewed
     */
    public function markAsReviewed($reviewerId): void
    {
        $this->update([
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }
}
