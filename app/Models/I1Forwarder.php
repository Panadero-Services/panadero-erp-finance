<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasPermissions;

class I1Forwarder extends Model
{
    use HasFactory, HasPermissions;

    protected $table = 'i1_forwarders';

    protected $fillable = [
        'identifier',
        'name',
        'address',
        'city',
        'postal_code',
        'i1_country_id',
        'email',
        'comment',
        'i1_user_id',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(I1Country::class, 'i1_country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'i1_user_id');
    }

    public static function validationRules(): array
    {
        return [
            'identifier' => 'required|string|max:16|unique:i1_forwarders,identifier',
            'name' => 'required|string|max:80',
            'address' => 'required|string|max:120',
            'city' => 'required|string|max:80',
            'postal_code' => 'required|string|max:16',
            'i1_country_id' => 'required|integer|exists:i1_countries,id',
            'email' => 'required|email|max:120',
            'comment' => 'nullable|string|max:255',
            'i1_user_id' => 'required|integer|exists:users,id',
            'is_active' => 'boolean',
            'is_locked' => 'boolean'
        ];
    }

    public static function getSearchableColumns(): array
    {
        return [
            'identifier',
            'name',
            'address',
            'city',
            'postal_code',
            'email',
            'comment'
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
            'identifier' => [
                'type' => 'input',
                'label' => 'Identifier',
                'col_span' => 2,
                'rows' => 1,
                'placeholder' => 'Enter identifier',
                'required' => true
            ],
            'name' => [
                'type' => 'input',
                'label' => 'Name',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter forwarder name',
                'required' => true
            ],
            'address' => [
                'type' => 'input',
                'label' => 'Address',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter address',
                'required' => true
            ],
            'city' => [
                'type' => 'input',
                'label' => 'City',
                'col_span' => 3,
                'rows' => 1,
                'placeholder' => 'Enter city',
                'required' => true
            ],
            'postal_code' => [
                'type' => 'input',
                'label' => 'Postal Code',
                'col_span' => 2,
                'rows' => 1,
                'placeholder' => 'Enter postal code',
                'required' => true
            ],
            'i1_country_id' => [
                'type' => 'select',
                'label' => 'Country',
                'col_span' => 3,
                'options' => self::getRelationOptions('country'),
                'help' => 'Select the country'
            ],
            'email' => [
                'type' => 'input',
                'label' => 'Email',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter email address',
                'required' => true
            ],
            'comment' => [
                'type' => 'textarea',
                'label' => 'Comment',
                'col_span' => 8,
                'rows' => 2,
                'placeholder' => 'Enter any additional information'
            ],
            'i1_user_id' => [
                'type' => 'select',
                'label' => 'User',
                'col_span' => 4,
                'options' => self::getRelationOptions('user'),
                'help' => 'Select the responsible user'
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
            ]
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            [
                'key' => 'id',
                'label' => 'ID',
                'type' => 'number',
                'width' => 'w-16',
                'formatter' => 'text',
                'sortable' => true
            ],
            [
                'key' => 'identifier',
                'label' => 'Identifier',
                'type' => 'text',
                'width' => 'w-28',
                'formatter' => 'text',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'width' => 'w-48',
                'formatter' => 'text',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'city',
                'label' => 'City',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'text',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'country',
                'label' => 'Country',
                'type' => 'relation',
                'width' => 'w-32',
                'formatter' => 'text',
                'relationField' => 'name',
                'relation' => 'country',
                'sortable' => true
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'type' => 'text',
                'width' => 'w-48',
                'formatter' => 'text',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'user',
                'label' => 'User',
                'type' => 'relation',
                'width' => 'w-32',
                'formatter' => 'text',
                'relationField' => 'name',
                'relation' => 'user',
                'sortable' => true
            ],
            [
                'key' => 'is_active',
                'label' => 'Active',
                'type' => 'boolean',
                'width' => 'w-20',
                'formatter' => 'boolean',
                'sortable' => true
            ],
            [
                'key' => 'is_locked',
                'label' => 'Locked',
                'type' => 'boolean',
                'width' => 'w-20',
                'formatter' => 'boolean',
                'sortable' => true
            ],
            [
                'key' => 'created_at',
                'label' => 'Created',
                'type' => 'datetime',
                'width' => 'w-32',
                'formatter' => 'date',
                'sortable' => true
            ],
            [
                'key' => 'updated_at',
                'label' => 'Updated',
                'type' => 'datetime',
                'width' => 'w-32',
                'formatter' => 'date',
                'sortable' => true
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
            'active' => 'Active',
            'inactive' => 'Inactive',
            'locked' => 'Locked'
        ];
    }

    public static function getContentFields(): array
    {
        return [
            'identifier',
            'name',
            'comment', 
            'city',
            'i1_country_id', 
            'is_active',
            'is_locked'
        ];
    }

    public static function getRelationOptions(string $relation): array
    {
        $options = [];
        
        switch ($relation) {
            case 'country':
                $countries = I1Country::where('is_active', true)->get();
                foreach ($countries as $country) {
                    $options[$country->id] = $country->name;
                }
                break;
                
            case 'user':
                // Remove the is_active condition since it doesn't exist
                $users = User::all();
                foreach ($users as $user) {
                    $options[$user->id] = $user->name;
                }
                break;
        }
        
        return $options;
    }

    public static function getPermissionAccess(): array
    {
        return [
            'globalRead' => [
                'description' => 'Full access to read all forwarders',
                'conditions' => [] // No conditions means full access
            ],
            'canReadUnlocked' => [
                'description' => 'Access to read all unlocked forwarders',
                'conditions' => [
                    'respect_lock' => true
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read own forwarders only',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }

    public static function getTitleColumn(): string
    {
        return 'name';
    }

    public static function getUserIdColumn(): string
    {
        return 'i1_user_id';
    }
} 