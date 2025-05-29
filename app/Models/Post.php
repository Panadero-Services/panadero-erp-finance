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
            'json' => 'required|string|min:2',
            'links' => 'nullable|string',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
            'is_published' => 'boolean',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
            'is_locked' => 'boolean',
            'is_self' => 'boolean',
            'is_smart' => 'boolean',
            'is_active' => 'boolean',
            'is_archived' => 'boolean'
        ];
    }

    /**
     * Get searchable columns for this model
     * @return array
     */
    public function getSearchableColumns(): array {
        return [
            'title',
            'body',
            'json',
            'id'
        ];
    }

    /**
     * Get searchable columns for this model
     * @return array
     */
    public function linksTable(): array {
        return [
            'relates_to_record',
            'previous_record',
            'next_record',
            'duplicates',
            'is_duplicated_by',
            'is_parent_for',
            'is_child_of',
            'next_chain'
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
                'col_span' => 3,
                'sequence' => 1,
                'options' => User::pluck('name', 'id'),
                'help' => 'Select the user who owns this post'
            ],
            'created_at' => [
                'type' => 'datetime',
                'label' => 'Created At',
                'col_span' => 3,
                'sequence' => 2,
                'readonly' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'label' => 'Updated At',
                'col_span' => 3,
                'sequence' => 3,
                'readonly' => true
            ],
            'title' => [
                'type' => 'textarea',
                'label' => 'Title',
                'col_span' => 8,
                'sequence' => 6,
                'rows' => 1,
                'placeholder' => 'Enter post title',
                'required' => true
            ],
            'body' => [
                'type' => 'textarea',
                'label' => 'Content',
                'col_span' => 5,
                'sequence' => 7,
                'rows' => 8,
                'required' => true
            ],
            'json' => [
                'type' => 'textarea',
                'label' => 'json',
                'col_span' => 3,
                'sequence' => 8,
                'rows' => 8,
                'help' => 'Use this to store unstructured data'
            ],
            'links' => [
                'type' => 'textarea',
                'label' => 'Links',
                'col_span' => 8,
                'sequence' => 9,
                'rows' => 2,
                'help' => 'Link this post to other posts with different relationships'
            ],

            'switches' => [
                'type' => 'label',
                'label' => 'switches',
                'col_span' => 8,
                'sequence' => 10,
                'help' => 'This label seperates the switches from the rest'
            ],

            'is_published' => [
                'type' => 'switch',
                'label' => 'Published',
                'col_span' => 1,
                'sequence' => 10,
                'default' => false
            ],
            'is_public' => [
                'type' => 'switch',
                'label' => 'Public',
                'col_span' => 1,
                'sequence' => 11,
                'default' => true
            ],
            'is_featured' => [
                'type' => 'switch',
                'label' => 'Featured',
                'col_span' => 1,
                'sequence' => 12,
                'default' => false
            ],
            'is_locked' => [
                'type' => 'switch',
                'label' => 'Locked',
                'col_span' => 1,
                'sequence' => 10,
                'default' => false
            ],
            'is_self' => [
                'type' => 'switch',
                'label' => 'Self',
                'col_span' => 1,
                'sequence' => 13,
                'default' => false
            ],
            'is_smart' => [
                'type' => 'switch',
                'label' => 'Smart',
                'col_span' => 1,
                'sequence' => 14,
                'default' => false
            ],           
            'is_active' => [
                'type' => 'switch',
                'label' => 'Active',
                'col_span' => 1,
                'sequence' => 15,
                'default' => false
            ],
            'is_archived' => [
                'type' => 'switch',
                'label' => 'Archived',
                'col_span' => 1,
                'sequence' => 16,
                'default' => false
            ],

        ];
    }
}