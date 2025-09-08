<?php

namespace Panadero\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'responsibilities',
        'department_id',
        'position_type',
        'employment_type',
        'experience_level',
        'location',
        'remote_allowed',
        'salary_min',
        'salary_max',
        'currency',
        'required_skills',
        'preferred_skills',
        'benefits',
        'application_deadline',
        'start_date',
        'status',
        'hiring_manager_id',
        'created_by',
        'max_applications',
        'current_applications',
        'internal_notes',
    ];

    protected $casts = [
        'remote_allowed' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'required_skills' => 'array',
        'preferred_skills' => 'array',
        'benefits' => 'array',
        'application_deadline' => 'date',
        'start_date' => 'date',
    ];

    /**
     * Get the department this vacancy belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the hiring manager
     */
    public function hiringManager(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'hiring_manager_id');
    }

    /**
     * Get the user who created this vacancy
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the applications for this vacancy
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Scope for published vacancies
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for active vacancies (published and not closed)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'draft']);
    }

    /**
     * Scope for remote-friendly vacancies
     */
    public function scopeRemoteFriendly($query)
    {
        return $query->where('remote_allowed', true);
    }

    /**
     * Scope for vacancies by experience level
     */
    public function scopeByExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    /**
     * Scope for vacancies by employment type
     */
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Check if vacancy is still accepting applications
     */
    public function isAcceptingApplications(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->max_applications && $this->current_applications >= $this->max_applications) {
            return false;
        }

        if ($this->application_deadline && $this->application_deadline->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Increment application count
     */
    public function incrementApplicationCount(): void
    {
        $this->increment('current_applications');
    }
}
