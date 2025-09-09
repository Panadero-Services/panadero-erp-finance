<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AITrainingData extends Model
{
    use HasFactory;

    protected $table = 'ai_training_data';
    
    protected $fillable = [
        'user_query',
        'ai_response',
        'model_used',
        'module',
        'confidence_score',
        'user_feedback',
        'session_id',
        'ip_address',
        'user_agent',
        'response_time_ms',
        'was_helpful',
        'suggested_improvement',
        'query_classification',
        'training_approved'
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'response_time_ms' => 'integer',
        'was_helpful' => 'boolean',
        'training_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope for approved training data
     */
    public function scopeApproved($query)
    {
        return $query->where('training_approved', true);
    }

    /**
     * Scope for specific modules
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for helpful responses
     */
    public function scopeHelpful($query)
    {
        return $query->where('was_helpful', true);
    }

    /**
     * Export training data in JSONL format for model training
     */
    public static function exportForTraining($module = null, $modelType = null)
    {
        $query = self::approved()->helpful();
        
        if ($module) {
            $query->where('module', $module);
        }
        
        if ($modelType) {
            $query->where('query_classification', $modelType);
        }
        
        return $query->get()->map(function ($record) {
            return [
                'prompt' => $record->user_query,
                'completion' => $record->ai_response,
                'category' => $record->module,
                'type' => $record->query_classification,
                'confidence' => $record->confidence_score,
                'feedback_score' => $record->was_helpful ? 1 : 0
            ];
        });
    }
}

