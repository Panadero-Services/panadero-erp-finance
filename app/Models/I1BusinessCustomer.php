<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasPermissions;

class I1BusinessCustomer extends Model
{
    use HasFactory, HasPermissions;

    protected $table = 'i1_business_customers';

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
        'default_qty',
        'i1_forwarder_id',
        'i1_product_id',
        'i1_source_site_id',
        'docs_array',
        'analyse_array',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'default_qty' => 'decimal:2',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(I1Country::class, 'i1_country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'i1_user_id');
    }

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(I1Forwarder::class, 'i1_forwarder_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(I1Product::class, 'i1_product_id');
    }

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(I1Site::class, 'i1_source_site_id');
    }

    public static function validationRules(): array
    {
        return [
            'identifier' => 'required|string|max:16|unique:i1_business_customers,identifier',
            'name' => 'required|string|max:80',
            'address' => 'required|string|max:120',
            'city' => 'required|string|max:80',
            'postal_code' => 'required|string|max:16',
            'i1_country_id' => 'required|integer|exists:i1_countries,id',
            'email' => 'required|email|max:120',
            'comment' => 'nullable|string|max:255',
            'i1_user_id' => 'required|integer|exists:users,id',
            'default_qty' => 'nullable|numeric|min:0',
            'i1_forwarder_id' => 'nullable|integer|exists:i1_forwarders,id',
            'i1_product_id' => 'nullable|integer|exists:i1_products,id',
            'i1_source_site_id' => 'nullable|integer|exists:i1_sites,id',
            'docs_array' => 'nullable|string|max:255',
            'analyse_array' => 'nullable|string|max:255',
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
            'comment',
            'docs_array',
            'analyse_array'
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
                'placeholder' => 'Enter customer name',
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
            'default_qty' => [
                'type' => 'number',
                'label' => 'Default Quantity',
                'col_span' => 2,
                'step' => '0.01',
                'help' => 'Default order quantity'
            ],
            'i1_forwarder_id' => [
                'type' => 'select',
                'label' => 'Default Forwarder',
                'col_span' => 4,
                'options' => self::getRelationOptions('forwarder'),
                'help' => 'Select default forwarder'
            ],
            'i1_product_id' => [
                'type' => 'select',
                'label' => 'Default Product',
                'col_span' => 4,
                'options' => self::getRelationOptions('product'),
                'help' => 'Select default product'
            ],
            'i1_source_site_id' => [
                'type' => 'select',
                'label' => 'Default Source Site',
                'col_span' => 4,
                'options' => self::getRelationOptions('sourceSite'),
                'help' => 'Select default source site'
            ],
            'docs_array' => [
                'type' => 'input',
                'label' => 'Documents Array',
                'col_span' => 4,
                'help' => 'Comma-separated list of documents'
            ],
            'analyse_array' => [
                'type' => 'input',
                'label' => 'Analysis Array',
                'col_span' => 4,
                'help' => 'Comma-separated list of analyses'
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
                'key' => 'default_qty',
                'label' => 'Default Qty',
                'type' => 'number',
                'width' => 'w-24',
                'formatter' => 'number',
                'sortable' => true
            ],
            [
                'key' => 'forwarder',
                'label' => 'Forwarder',
                'type' => 'relation',
                'width' => 'w-32',
                'formatter' => 'text',
                'relationField' => 'name',
                'relation' => 'forwarder',
                'sortable' => true
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
                $users = User::all();
                foreach ($users as $user) {
                    $options[$user->id] = $user->name;
                }
                break;
                
            case 'forwarder':
                $forwarders = I1Forwarder::where('is_active', true)->get();
                foreach ($forwarders as $forwarder) {
                    $options[$forwarder->id] = $forwarder->name ?? $forwarder->identifier;
                }
                break;
                
            case 'product':
                $products = I1Product::where('is_active', true)->get();
                foreach ($products as $product) {
                    $options[$product->id] = $product->name ?? $product->identifier;
                }
                break;
                
            case 'sourceSite':
                $sites = I1Site::where('is_active', true)->get();
                foreach ($sites as $site) {
                    $options[$site->id] = $site->name ?? $site->identifier;
                }
                break;
        }
        
        return $options;
    }

    public static function getPermissionAccess(): array
    {
        return [
            'globalRead' => [
                'description' => 'Full access to read all business customers',
                'conditions' => []
            ],
            'canReadUnlocked' => [
                'description' => 'Access to read all unlocked business customers',
                'conditions' => [
                    'respect_lock' => true
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read own business customers only',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }

    public static function getContentFields(): array
    {
        return [
            'name',
            'identifier',
            'city',
            'is_active',
            'is_locked'
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