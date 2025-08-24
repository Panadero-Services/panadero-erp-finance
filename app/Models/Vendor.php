<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasPermissions;

class Vendor extends BaseModel
{
    use HasFactory, SoftDeletes, HasPermissions;

    protected $fillable = [
        'vendor_code',
        'name',
        'legal_name',
        'contact_person',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'tax_id',
        'registration_number',
        'vendor_type',
        'status',
        'metadata',
        'external_id',
        'is_approved',
        'approved_at',
        'approved_by',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_approved' => 'boolean',
        'approved_at' => 'timestamp',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'metadata'
    ];

    /**
     * Get the finance vendor record associated with this vendor.
     */
    public function financeVendor(): HasOne
    {
        return $this->hasOne(FinanceVendor::class);
    }

    /**
     * Get the user who approved this vendor.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Validation rules for the vendor model
     */
    public static function validationRules(): array
    {
        return [
            'vendor_code' => 'required|string|max:50|unique:vendors,vendor_code',
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'vendor_type' => 'required|in:supplier,service_provider,contractor,consultant,other',
            'status' => 'required|in:active,inactive,suspended,pending',
            'metadata' => 'nullable|array',
            'external_id' => 'nullable|string|max:255',
            'is_approved' => 'boolean',
            'approved_by' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'is_locked' => 'boolean'
        ];
    }

    /**
     * Get searchable columns for this model
     */
    public static function getSearchableColumns(): array
    {
        return [
            'vendor_code',
            'name',
            'legal_name',
            'contact_person',
            'email',
            'phone',
            'city',
            'tax_id',
            'registration_number'
        ];
    }

    /**
     * Get the permission access configuration for this model.
     */
    public static function getPermissionAccess(): array
    {
        return [
            'globalRead' => [
                'description' => 'Full access to read all vendors',
                'conditions' => []
            ],
            'canReadActive' => [
                'description' => 'Access to read active vendors only',
                'conditions' => [
                    'status_filter' => 'active',
                    'respect_lock' => true
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read vendors created by user',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }

    /**
     * Scope a query to only include active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    /**
     * Scope a query to only include approved vendors.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to filter by vendor type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }

    // ===== MISSING FUNCTIONS BASED ON FUTURE.PHP MODEL =====

    /**
     * Define links table structure for vendor relationships
     */
    public static function linksTable(): array
    {
        return [
            'relates_to',
            'previous',
            'next',
            'parent_vendor',
            'subsidiary_of',
            'partner_with',
            'supplier_to',
            'client_of',
            'competes_with'
        ];
    }

    /**
     * Define form fields for vendor creation/editing
     */
    public static function formFields(): array
    {
        return [
            'vendor_code' => [
                'type' => 'input',
                'label' => 'Vendor Code',
                'col_span' => 3,
                'placeholder' => 'Enter unique vendor code',
                'required' => true
            ],
            'name' => [
                'type' => 'input',
                'label' => 'Vendor Name',
                'col_span' => 5,
                'placeholder' => 'Enter vendor business name',
                'required' => true
            ],
            'legal_name' => [
                'type' => 'input',
                'label' => 'Legal Name',
                'col_span' => 4,
                'placeholder' => 'Enter legal registered name'
            ],
            'vendor_type' => [
                'type' => 'select',
                'label' => 'Vendor Type',
                'col_span' => 3,
                'options' => self::getVendorTypeOptions(),
                'required' => true
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col_span' => 2,
                'options' => self::getStatusOptions(),
                'default' => 'active',
                'required' => true
            ],
            'contact_person' => [
                'type' => 'input',
                'label' => 'Contact Person',
                'col_span' => 4,
                'placeholder' => 'Primary contact name'
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email',
                'col_span' => 4,
                'placeholder' => 'vendor@company.com'
            ],
            'phone' => [
                'type' => 'input',
                'label' => 'Phone',
                'col_span' => 3,
                'placeholder' => '+1-555-0123'
            ],
            'website' => [
                'type' => 'url',
                'label' => 'Website',
                'col_span' => 4,
                'placeholder' => 'https://www.vendor.com'
            ],
            'address' => [
                'type' => 'textarea',
                'label' => 'Address',
                'col_span' => 6,
                'rows' => 2,
                'placeholder' => 'Street address'
            ],
            'city' => [
                'type' => 'input',
                'label' => 'City',
                'col_span' => 3,
                'placeholder' => 'City'
            ],
            'state_province' => [
                'type' => 'input',
                'label' => 'State/Province',
                'col_span' => 3,
                'placeholder' => 'State or Province'
            ],
            'postal_code' => [
                'type' => 'input',
                'label' => 'Postal Code',
                'col_span' => 2,
                'placeholder' => '12345'
            ],
            'country' => [
                'type' => 'select',
                'label' => 'Country',
                'col_span' => 2,
                'options' => self::getCountryOptions(),
                'placeholder' => 'Select country'
            ],
            'tax_id' => [
                'type' => 'input',
                'label' => 'Tax ID',
                'col_span' => 3,
                'placeholder' => 'Tax identification number'
            ],
            'registration_number' => [
                'type' => 'input',
                'label' => 'Registration Number',
                'col_span' => 3,
                'placeholder' => 'Business registration number'
            ],
            'external_id' => [
                'type' => 'input',
                'label' => 'External ID',
                'col_span' => 3,
                'placeholder' => 'External system identifier'
            ],
            'approved_by' => [
                'type' => 'select',
                'label' => 'Approved By',
                'col_span' => 3,
                'options' => self::getRelationOptions('user'),
                'help' => 'Select the user who approved this vendor'
            ],
            'approved_at' => [
                'type' => 'datetime',
                'label' => 'Approved At',
                'col_span' => 3,
                'readonly' => true
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
            'metadata' => [
                'type' => 'json',
                'label' => 'Metadata',
                'col_span' => 8,
                'help' => 'Additional vendor-specific data in JSON format'
            ],
            'switches' => [
                'type' => 'label',
                'label' => 'Settings',
                'col_span' => 8,
                'help' => 'Vendor status and permission settings'
            ],
            'is_approved' => [
                'type' => 'switch',
                'label' => 'Approved',
                'col_span' => 2,
                'default' => false
            ],
            'is_active' => [
                'type' => 'switch',
                'label' => 'Active',
                'col_span' => 2,
                'default' => true
            ],
            'is_locked' => [
                'type' => 'switch',
                'label' => 'Locked',
                'col_span' => 2,
                'default' => false
            ]
        ];
    }

    /**
     * Define table columns for data display
     */
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
                'key' => 'vendor_code',
                'label' => 'Code',
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text',
                'clickable' => true
            ],
            [
                'key' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'width' => 'w-48',
                'formatter' => 'text',
                'clickable' => true
            ],
            [
                'key' => 'vendor_type',
                'label' => 'Type',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'vendor_type'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'status',
                'width' => 'w-24',
                'formatter' => 'status'
            ],
            [
                'key' => 'contact_person',
                'label' => 'Contact',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'text'
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'width' => 'w-48',
                'formatter' => 'email'
            ],
            [
                'key' => 'city',
                'label' => 'City',
                'type' => 'text',
                'width' => 'w-24',
                'formatter' => 'text'
            ],
            [
                'key' => 'country',
                'label' => 'Country',
                'type' => 'text',
                'width' => 'w-20',
                'formatter' => 'text'
            ],
            [
                'key' => 'is_approved',
                'label' => 'Approved',
                'type' => 'boolean',
                'width' => 'w-20',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'is_active',
                'label' => 'Active',
                'type' => 'boolean',
                'width' => 'w-20',
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
                'key' => 'actions',
                'label' => 'Actions',
                'type' => 'actions',
                'width' => 'w-24',
                'formatter' => 'actions'
            ]
        ];
    }

    /**
     * Get status mapping with colors for UI display
     */
    public static function getStatusMapping(): array
    {
        return [
            'active' => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-400/20',
            'inactive' => 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-gray-500/10 dark:ring-gray-400/20',
            'suspended' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 ring-red-600/10 dark:ring-red-400/20',
            'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20'
        ];
    }

    /**
     * Get content fields for vendor information
     */
    public static function getContentFields(): array
    {
        return [
            'legal_name', 'contact_person', 'email', 'phone', 'website', 
            'address', 'city', 'state_province', 'postal_code', 'country',
            'tax_id', 'registration_number'
        ];
    }

    /**
     * Get vendor type options
     */
    public static function getVendorTypeOptions(): array
    {
        return [
            'supplier' => 'Supplier',
            'service_provider' => 'Service Provider',
            'contractor' => 'Contractor',
            'consultant' => 'Consultant',
            'other' => 'Other'
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'pending' => 'Pending'
        ];
    }

    /**
     * Get country options (subset for demo)
     */
    public static function getCountryOptions(): array
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'UK' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'AU' => 'Australia',
            'JP' => 'Japan',
            'CN' => 'China'
        ];
    }

    /**
     * Get relation options for form fields
     */
    public static function getRelationOptions(string $relation): array
    {
        $relationMap = [
            'user' => ['model' => User::class, 'display' => 'name'],
        ];
        
        if (isset($relationMap[$relation])) {
            $config = $relationMap[$relation];
            return $config['model']::pluck($config['display'], 'id')->toArray();
        }
        
        return [];
    }

    /**
     * Define metadata format for structured data storage
     */
    public static function metadataFormat(): array
    {
        return [
            [
                'name' => 'industry',
                'type' => 'text',
                'label' => 'Industry',
                'required' => false
            ],
            [
                'name' => 'certifications',
                'type' => 'array',
                'label' => 'Certifications',
                'required' => false
            ],
            [
                'name' => 'credit_rating',
                'type' => 'select',
                'label' => 'Credit Rating',
                'options' => ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C', 'Not Rated'],
                'required' => false
            ],
            [
                'name' => 'annual_revenue',
                'type' => 'number',
                'label' => 'Annual Revenue',
                'required' => false
            ]
        ];
    }

    // ===== ADDITIONAL SCOPE METHODS =====

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by country
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to filter pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false)->where('status', 'pending');
    }

    /**
     * Scope to filter by vendor type and status
     */
    public function scopeActiveOfType($query, $type)
    {
        return $query->where('vendor_type', $type)
                    ->where('status', 'active')
                    ->where('is_active', true);
    }

    // ===== UTILITY METHODS =====

    /**
     * Get full address as a single string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country ? strtoupper($this->country) : null
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if vendor needs approval
     */
    public function needsApproval(): bool
    {
        return !$this->is_approved && $this->status === 'pending';
    }

    /**
     * Check if vendor is fully active and operational
     */
    public function isOperational(): bool
    {
        return $this->is_approved && 
               $this->status === 'active' && 
               $this->is_active && 
               !$this->is_locked;
    }

    /**
     * Get vendor display name (with fallback)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->legal_name ?: $this->name;
    }

    /**
     * Get the column name to use as title in displays
     */
    public static function getTitleColumn(): string
    {
        return 'name'; // or 'vendor_code' or any other column you prefer
    }

    /**
     * Get user ID column name for permission filtering
     */
    public static function getUserIdColumn(): string
    {
        return 'approved_by'; // or 'created_by' if you prefer
    }
}

