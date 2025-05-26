<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    /**
     * Validation rules for the post model
     * @return array
     */
    public static function validationRules(): array {
        return [
            'user_id' => 'nullable|integer',
            'title' => 'required|string|max:128|min:8',
            'body' => 'required|string|max:1024',
            'json' => 'nullable|string',
            'published' => 'boolean',
            'public' => 'boolean',
            'featured' => 'boolean',
            'locked' => 'boolean',
            'self' => 'boolean',
            'smart' => 'boolean',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date'
        ];
    }

    /**
     * Form configuration for the post model
     * @return array
     */
    public static function formFields(): array {
        return [
            'user_id' => [
                'type' => 'select',
                'label' => 'User',
                'col_span' => 2,
                'sequence' => 1,
                'options' => User::pluck('name', 'id'),
                'help' => 'Select the user who owns this post'
            ],
            'title' => [
                'type' => 'textarea',
                'label' => 'Title',
                'col_span' => 3,
                'sequence' => 3,
                'placeholder' => 'Enter post title',
                'required' => true
            ],
            'json' => [
                'type' => 'textarea',
                'label' => 'JSON Data',
                'col_span' => 3,
                'sequence' => 3,
                'rows' => 3,
                'help' => 'Optional JSON data for the post'
            ],
            'body' => [
                'type' => 'textarea',
                'label' => 'Content',
                'col_span' => 6,
                'sequence' => 4,
                'rows' => 5,
                'required' => true
            ],

            'published' => [
                'type' => 'switch',
                'label' => 'Published',
                'col_span' => 1,
                'sequence' => 7,
                'default' => false
            ],
            'public' => [
                'type' => 'switch',
                'label' => 'Public',
                'col_span' => 1,
                'sequence' => 8,
                'default' => true
            ],
            'featured' => [
                'type' => 'switch',
                'label' => 'Featured',
                'col_span' => 1,
                'sequence' => 9,
                'default' => false
            ],
            'locked' => [
                'type' => 'switch',
                'label' => 'Locked',
                'col_span' => 1,
                'sequence' => 10,
                'default' => false
            ],
            'self' => [
                'type' => 'switch',
                'label' => 'Self',
                'col_span' => 1,
                'sequence' => 11,
                'default' => false
            ],
            'smart' => [
                'type' => 'switch',
                'label' => 'Smart',
                'col_span' => 1,
                'sequence' => 12,
                'default' => false
            ],
            'created_at' => [
                'type' => 'datetime',
                'label' => 'Created At',
                'col_span' => 2,
                'sequence' => 2,
                'readonly' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'label' => 'Updated At',
                'col_span' => 2,
                'sequence' => 2,
                'readonly' => true
            ]
        ];
    }
}