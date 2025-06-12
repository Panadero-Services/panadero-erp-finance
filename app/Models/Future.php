<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Future extends Model
{
    use HasFactory;

    protected $fillable = [
        'item',
        'title',
        'description',
        'color',
        'version',
        'icon',
        'status',
        'user_id',
        'project_id',
        'json',
        'links',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'json' => 'array',
        'links' => 'array',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function validationRules(): array
    {
        return [
            'item' => 'required|string|min:4|max:12',
            'title' => 'required|string|min:1|max:80',
            'description' => 'required|string|max:2048',
            'user_id' => 'required|integer|exists:users,id',
            'icon' => 'nullable|string|max:80',
            'status' => 'required|string|min:1|max:32',
            'project_id' => 'required|integer|exists:projects,id',
            'json' => 'required|array',
            'links' => 'required|array',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    public function getSearchableColumns(): array
    {
        return [
            'item',
            'title',
            'description'
        ];
    }

    public static function linksTable(): array
    {
        return [
            'relates_to',
            'previous',
            'next',
            'duplicates',
            'duplicated_by',
            'child_of',
            'parent_for',
            'chains',
            'chained_by'
        ];
    }

    public static function formFields(): array
    {
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
            'item' => [
                'type' => 'textarea',
                'label' => 'Item',
                'col_span' => 8,
                'sequence' => 6,
                'rows' => 1,
                'placeholder' => 'Enter post item',
                'required' => true
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
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'col_span' => 5,
                'sequence' => 7,
                'rows' => 6,
                'required' => true
            ],
            'icon' => [
                'type' => 'input',
                'label' => 'Icon',
                'col_span' => 4,
                'sequence' => 6,
                'rows' => 1,
                'placeholder' => 'Enter icon',
                'required' => true
            ],
            'status' => [
                'type' => 'input',
                'label' => 'Status',
                'col_span' => 4,
                'sequence' => 6,
                'rows' => 1,
                'placeholder' => 'Enter status',
                'required' => true
            ],
            'json' => [
                'type' => 'textarea',
                'label' => 'JSON',
                'col_span' => 3,
                'sequence' => 8,
                'rows' => 6,
                'help' => 'Use this to store unstructured data'
            ],
            'links' => [
                'type' => 'textarea',
                'label' => 'Links',
                'col_span' => 8,
                'sequence' => 9,
                'rows' => 3,
                'help' => 'Link this post to other posts with different relationships'
            ],
            'switches' => [
                'type' => 'label',
                'label' => 'Switches',
                'col_span' => 8,
                'sequence' => 10,
                'help' => 'This label separates the switches from the rest'
            ],
            'is_active' => [
                'type' => 'switch',
                'label' => 'Active',
                'col_span' => 1,
                'sequence' => 11,
                'default' => true
            ],
            'is_locked' => [
                'type' => 'switch',
                'label' => 'Locked',
                'col_span' => 1,
                'sequence' => 12,
                'default' => false
            ]
        ];
    }
} 