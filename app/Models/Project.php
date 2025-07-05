<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\Traits\HasPermissions;

class Project extends Model
{
    use HasFactory, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'color',
        'status',
        'json',
        'links',
        'user_id',
        'is_active'
    ];

    protected $casts = [
        'json' => 'array',
        'links' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * JaW says...:
     * 
     * Set Validation Rules HERE!!!!!
     *
     * @return array<string, string>
     */
    protected function validRules(): array
    {
        return [
            'title' => 'required|string|min:4',
            'title' => 'required|string|max:18'
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'json' => 'array',
            'links' => 'array'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function validationRules(): array
    {
        return [
            'title' => 'required|string|max:80|min:1',
            'description' => 'required|string',
            'color' => 'nullable|string|max:24',
            'status' => 'nullable|string|max:32',
            'json' => 'nullable|array',
            'links' => 'nullable|array',
            'user_id' => 'required|exists:users,id',
            'is_active' => 'boolean'
        ];
    }

    public static function getSearchableColumns(): array
    {
        return [
            'title',
            'description',
            'status',
            'id'
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
                'key' => 'is_active',
                'label' => 'Active',
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
            'blocked' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 ring-red-600/10 dark:ring-red-400/20',
            'fly_in_the_sky' => 'bg-indigo-50 dark:bg-blue-900/20 text-indigo-700 dark:text-indigo-300 ring-indigo-600/10 dark:ring-indigo-400/20',
            'review' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20'
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
            // Add more relations as needed
        ];
        
        if (isset($relationMap[$relation])) {
            $config = $relationMap[$relation];
            return $config['model']::pluck($config['display'], 'id')->toArray();
        }
        
        return [];
    }

    public static function formFields(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'col_span' => 8,
                'sequence' => 1,
                'placeholder' => 'Enter project title',
                'required' => true
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'col_span' => 8,
                'sequence' => 2,
                'rows' => 2,
                'required' => true
            ],
            'user_id' => [
                'type' => 'select',
                'label' => 'User',
                'col_span' => 4,
                'sequence' => 5,
                'options' => self::getRelationOptions('user'),
                'help' => 'Select the user who owns this project',
                'required' => true
            ],

            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col_span' => 2,
                'sequence' => 4,
                'default' => 'idle'
            ],

            'color' => [
                'type' => 'select',
                'label' => 'Color',
                'col_span' => 2,
                'sequence' => 3,
                'options' => self::getColorOptions(),
                'default' => 'blue'
            ],

   
            'json' => [
                'type' => 'textarea',
                'label' => 'JSON Data',
                'col_span' => 8,
                'sequence' => 6,
                'rows' => 2,
                'help' => 'Use this to store unstructured data in JSON format'
            ],
            'links' => [
                'type' => 'textarea',
                'label' => 'Links',
                'col_span' => 8,
                'sequence' => 7,
                'rows' => 2,
                'help' => 'Link this project to other projects with different relationships'
            ],
            'is_active' => [
                'type' => 'switch',
                'label' => 'Active',
                'col_span' => 1,
                'sequence' => 8,
                'default' => true
            ]
        ];
    }

    /**
     * Define permission-based access for projects
     */
    public static function getPermissionAccess(): array
    {
        return [
            'canReadAll' => [
                'description' => 'Full access to read all projects',
                'conditions' => []
            ],
            'canReadActive' => [
                'description' => 'Access to read active projects',
                'conditions' => [
                    'active_only' => true,
                    'respect_lock' => true
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read own projects',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }
}
