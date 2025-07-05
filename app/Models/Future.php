<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Project;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasPermissions;

class Future extends Model
{
    use HasFactory, HasPermissions;

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
        'options',
        'links',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'options' => 'array',
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
            'options' => 'required',
            'links' => 'required',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    public static function getSearchableColumns(): array
    {
        return [
            'item',
            'title',
            'description',
            'options',
            'links'
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
            'title' => [
                'type' => 'input',
                'label' => 'Title',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter post title',
                'required' => true
            ],

            'item' => [
                'type' => 'input',
                'label' => 'Item',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter post item',
                'required' => true
            ],

            'user_id' => [
                'type' => 'select',
                'label' => 'User',
                'col_span' => 4,
                'options' => self::getRelationOptions('user'),
                'help' => 'Select the user who owns this record'
            ],

            'created_at' => [
                'type' => 'datetime',
                'label' => 'Created At',
                'col_span' => 2,
                'readonly' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'label' => 'Updated At',
                'col_span' => 2,
                'readonly' => true
            ],

            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'col_span' => 8,
                'rows' => 2,
                'required' => true
            ],
            'project_id' => [
                'type' => 'select',
                'label' => 'Project',
                'col_span' => 4,
                'options' => self::getRelationOptions('project'),
                'help' => 'Select the project for this record'
            ],

            'icon' => [
                'type' => 'input',
                'label' => 'Icon',
                'col_span' => 2,
                'rows' => 1,
                'placeholder' => 'Enter icon',
                'required' => true
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col_span' => 2,
                'default' => 'idle',
                'required' => true
            ],
            'options' => [
                'type' => 'json',
                'label' => 'Options',
                'col_span' => 8,
                'help' => 'Use this to store unstructured data'
            ],
            'links' => [
                'type' => 'links',
                'label' => 'Links',
                'col_span' => 8,
                'help' => 'Link this post to other posts with different relationships'
            ],
            'switches' => [
                'type' => 'label',
                'label' => 'Switches',
                'col_span' => 8,
                'help' => 'This label separates the switches from the rest'
            ],
            'is_active' => [
                'type' => 'switch',
                'label' => 'Active',
                'col_span' => 1,
                'default' => true
            ],
            'is_locked' => [
                'type' => 'switch',
                'label' => 'Locked',
                'col_span' => 1,
                'default' => false
            ],
            'color' => [
                'type' => 'select',
                'label' => 'Color',
                'col_span' => 2,
                'options' => self::getColorOptions(),
                'default' => 'blue'
            ]
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            [
                'key' => 'id',
                'label' => 'ID',
                'type' => 'text',
                'width' => 'w-16',
                'formatter' => 'text'
            ],
            [
                'key' => 'item',
                'label' => 'Item',
                'type' => 'text',
                'width' => 'w-32',
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
                'key' => 'description',
                'label' => 'Description',
                'type' => 'text',
                'width' => 'w-64',
                'formatter' => 'truncate'
            ],
            [
                'key' => 'color',
                'label' => 'Color',
                'type' => 'color',
                'width' => 'w-24',
                'formatter' => 'color'
            ],
            [
                'key' => 'version',
                'label' => 'Version',
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text'
            ],
            [
                'key' => 'icon',
                'label' => 'Icon',
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'status',
                'width' => 'w-32',
                'formatter' => 'status'
            ],
            [
                'key' => 'user',
                'label' => 'User',
                'type' => 'relation',
                'width' => 'w-32',
                'formatter' => 'text',
                'relationField' => 'name'
            ],
            [
                'key' => 'project',
                'label' => 'Project',
                'type' => 'relation',
                'width' => 'w-32',
                'formatter' => 'text',
                'relationField' => 'title'
            ],
            [
                'key' => 'is_active',
                'label' => 'Active',
                'type' => 'boolean',
                'width' => 'w-24',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_locked',
                'label' => 'Locked',
                'type' => 'boolean',
                'width' => 'w-24',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'created_at',
                'label' => 'Created',
                'type' => 'date',
                'width' => 'w-32',
                'formatter' => 'date'
            ],
            [
                'key' => 'updated_at',
                'label' => 'Updated',
                'type' => 'date',
                'width' => 'w-32',
                'formatter' => 'date'
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

    public static function getStatusMapping(): array
    {
        return [
            'idle' => 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-gray-500/10 dark:ring-gray-400/20',
            'in_progress' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-blue-700/10 dark:ring-blue-400/20',
            'completed' => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-400/20',
            'stealth_mode' => 'bg-indigo-50 dark:bg-blue-900/20 text-indigo-700 dark:text-indigo-300 ring-indigo-600/10 dark:ring-indigo-400/20',
            'blocked' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 ring-red-600/10 dark:ring-red-400/20',
            'review' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20'
        ];
    }

    public static function getContentFields(): array
    {
        return [
            'description', 'color', 'version', 'icon'
        ];
    }

    public static function getColorOptions(): array
    {
        return [
            'blue' => 'Blue',
            'green' => 'Green',
            'red' => 'Red',
            'yellow' => 'Yellow',
            'purple' => 'Purple',
            'orange' => 'Orange',
            'gray' => 'Gray',
            'pink' => 'Pink'
        ];
    }

    public static function getRelationOptions(string $relation): array
    {
        $relationMap = [
            'user' => ['model' => User::class, 'display' => 'name'],
            'project' => ['model' => Project::class, 'display' => 'title'],
        ];
        
        if (isset($relationMap[$relation])) {
            $config = $relationMap[$relation];
            return $config['model']::pluck($config['display'], 'id')->toArray();
        }
        
        return [];
    }


// For a model with color options
public static function optionsFormat(): array
{
    return [
        [
            'name' => 'name',
            'type' => 'text',
            'label' => 'Color Name',
            'required' => true
        ],
        [
            'name' => 'hex',
            'type' => 'color',
            'label' => 'Color Code',
            'required' => true
        ],
        [
            'name' => 'opacity',
            'type' => 'number',
            'label' => 'Opacity',
            'min' => 0,
            'max' => 1,
            'step' => 0.1,
            'required' => false
        ]
    ];
}



    

    /**
     * Define permission-based access for futures
     * Override the trait's default implementation
     */
    public static function getPermissionAccess(): array
    {
        return [
            'canReadAll' => [
                'description' => 'Full access to read all futures',
                'conditions' => [] // No conditions means full access
            ],
            'canReadUnlocked' => [
                'description' => 'Access to read all unlocked futures',
                'conditions' => [
                    'respect_lock' => true
                ]
            ],
            'canReadByStatus' => [
                'description' => 'Access to read specific status futures',
                'conditions' => [
                    'project_based' => true,
                    'respect_lock' => true,
                    'status_allowed' => ['idle', 'in_progress', 'review']
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read own futures only',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true,
                    'status_allowed' => ['completed', 'review']
                ]
            ]
        ];
    }

   
} 