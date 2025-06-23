<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;


class Project extends Model
{
    use HasFactory;

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
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'text'
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
            'is_active' => [
                'true' => 'Active',
                'false' => 'Inactive'
            ]
        ];
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
                'options' => User::pluck('name', 'id'),
                'help' => 'Select the user who owns this project',
                'required' => true
            ],

            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col_span' => 2,
                'sequence' => 4,
                'options' => [
                    'idle' => 'Idle',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'blocked' => 'Blocked',
                    'review' => 'Review'
                ],
                'default' => 'idle'
            ],


            'color' => [
                'type' => 'select',
                'label' => 'Color',
                'col_span' => 2,
                'sequence' => 3,
                'options' => [
                    'blue' => 'Blue',
                    'green' => 'Green',
                    'red' => 'Red',
                    'yellow' => 'Yellow',
                    'purple' => 'Purple',
                    'orange' => 'Orange',
                    'gray' => 'Gray',
                    'pink' => 'Pink'
                ],
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
}
