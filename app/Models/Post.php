<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\HasSlug;
use App\Models\Traits\HasPermissions;

class Post extends Model
{
    use HasFactory, HasSlug, HasPermissions;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'options',
        'links',
        'category_id',
        'slug',
        'is_published',
        'is_public',
        'is_featured',
        'is_locked',
        'is_self',
        'is_smart',
        'is_active',
        'is_archived'
    ];

    protected $casts = [
        'options' => 'array',
        'links' => 'array',
        'is_published' => 'boolean',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'is_locked' => 'boolean',
        'is_self' => 'boolean',
        'is_smart' => 'boolean',
        'is_active' => 'boolean',
        'is_archived' => 'boolean'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the post type that owns the post.
     */
    public function postType(): BelongsTo
    {
        return $this->belongsTo(PostType::class);
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Validation rules for the post model
     * @return array
     */
    public static function validationRules(): array {
        return [
            'user_id' => 'nullable|integer',
            'title' => 'required|string|max:128|min:8',
            'body' => 'required|string|min:24|max:2048',
            'options' => 'nullable|array',
            'links' => 'nullable|array',
            'category_id' => 'nullable|integer|exists:categories,id',
            'slug' => 'nullable|string|max:128',  // Changed from 'required' to 'nullable'
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
    public static function getSearchableColumns(): array {
        return [
            'title',
            'body',
            'options',
            'id'
        ];
    }

    /**
     * Get links table options for this model
     * @return array
     */
    public static function linksTable(): array {
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

    /**
     * Get table columns configuration for dynamic table
     * @return array
     */
    public static function getTableColumns(): array {
        return [
            [
                'key' => 'id',
                'label' => 'ID',
                'type' => 'text',
                'width' => 'w-16',
                'formatter' => 'text'
            ],
            [
                'key' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'width' => 'w-48',
                'formatter' => 'text',
                'clickable' => true
            ],
            [
                'key' => 'body',
                'label' => 'Content',
                'type' => 'text',
                'width' => 'w-48',
                'formatter' => 'truncate'
            ],
            [
                'key' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'text'
            ],
            [
                'key' => 'user_id',
                'label' => 'User',
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text'
            ],
            [
                'key' => 'category_id',
                'label' => 'Category',
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text'
            ],
            [
                'key' => 'is_published',
                'label' => 'Published',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_public',
                'label' => 'Public',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_featured',
                'label' => 'Featured',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_locked',
                'label' => 'Locked',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_self',
                'label' => 'Self',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_smart',
                'label' => 'Smart',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_active',
                'label' => 'Active',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_archived',
                'label' => 'Archived',
                'type' => 'boolean',
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'options',
                'label' => 'Options',
                'type' => 'json',
                'width' => 'w-24',
                'formatter' => 'json'
            ],
            [
                'key' => 'links',
                'label' => 'Links',
                'type' => 'json',
                'width' => 'w-24',
                'formatter' => 'json'
            ],
            [
                'key' => 'actions',
                'label' => 'Actions',
                'type' => 'actions',
                'width' => 'w-24',
                'formatter' => 'actions'
            ]
        ];
    }

    /**
     * Get status mapping for the model
     * @return array
     */
    public static function getStatusMapping(): array {
        return [
            'draft' => 'gray',
            'published' => 'green',
            'archived' => 'red',
            'featured' => 'purple',
            'locked' => 'amber',
            'public' => 'blue',
            'private' => 'orange'
        ];
    }

    /**
     * Get color options for the model
     * @return array
     */
    public static function getColorOptions(): array {
        return [
            'red' => 'Red',
            'blue' => 'Blue',
            'green' => 'Green',
            'yellow' => 'Yellow',
            'purple' => 'Purple',
            'indigo' => 'Indigo',
            'pink' => 'Pink',
            'gray' => 'Gray',
            'orange' => 'Orange',
            'teal' => 'Teal',
            'cyan' => 'Cyan',
            'emerald' => 'Emerald',
            'lime' => 'Lime',
            'amber' => 'Amber',
            'rose' => 'Rose',
            'violet' => 'Violet',
            'fuchsia' => 'Fuchsia',
            'slate' => 'Slate',
            'zinc' => 'Zinc',
            'neutral' => 'Neutral',
            'stone' => 'Stone',
            'turquoise' => 'Turquoise'
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
                'col_span' => 8,
                'sequence' => 8,
                'rows' => 3,
                'required' => true
            ],
            'options' => [
                'type' => 'json',
                'label' => 'Options',
                'col_span' => 3,
                'sequence' => 9,
                'help' => 'Use this to store structured data as JSON'
            ],
            'links' => [
                'type' => 'textarea',
                'label' => 'Links',
                'col_span' => 8,
                'sequence' => 10,
                'rows' => 3,
                'help' => 'Link this post to other posts with different relationships'
            ],

            'switches' => [
                'type' => 'label',
                'label' => 'switches',
                'col_span' => 8,
                'sequence' => 11,
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

    /**
     * Get content fields for the model
     * @return array
     */
    public static function getContentFields(): array {
        return [
             'body', 'is_active', 'is_archived', 'is_smart'
        ];
    }


// For a model with API endpoint options
public static function optionsFormat(): array
{
    return [
        [
            'name' => 'endpoint',
            'type' => 'text',
            'label' => 'API Endpoint',
            'required' => true
        ],
        [
            'name' => 'method',
            'type' => 'select',
            'label' => 'HTTP Method',
            'options' => ['GET', 'POST', 'PUT', 'DELETE'],
            'required' => true
        ],
        [
            'name' => 'headers',
            'type' => 'json',
            'label' => 'Headers',
            'required' => false
        ]
    ];
}

/**
 * Define permission-based access for posts
 */
public static function getPermissionAccess(): array
{
    return [
        'globalRead' => [
            'description' => 'Full access to read all posts',
            'conditions' => []
        ],
        'canReadPublished' => [
            'description' => 'Access to read published posts',
            'conditions' => [
                'published_only' => true,
                'respect_lock' => true
            ]
        ],
        'canReadOwn' => [
            'description' => 'Access to read own posts',
            'conditions' => [
                'owner_only' => true,
                'respect_lock' => true
            ]
        ],
        'canReadPublic' => [
            'description' => 'Access to read public posts',
            'conditions' => [
                'published_only' => true,
                'public_only' => true,
                'respect_lock' => true
            ]
        ]
    ];
}

    
}