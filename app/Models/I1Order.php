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

class I1Order extends Model
{
    use HasFactory, HasPermissions;

    protected $table = 'i1_orders';

    protected $fillable = [
        'tpt_order_nr',
        'cust_order_nr',
        'i1_customer_id',
        'i1_supplier_id',
        'r_date',
        'e_date',
        'a_date',
        'b_date',
        'i1_forwarder_id',
        'i1_source_site_id',
        'i1_dest_site_id',
        'i1_product_id',
        'order_qty',
        'i1_storage_from_id',
        'i1_storage_to_id',
        'weight1',
        'weight2',
        'net_qty',
        'density',
        'purity',
        'docs_array',
        'analyse_array',
        'i1_status_id',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'r_date' => 'datetime',
        'e_date' => 'datetime',
        'a_date' => 'datetime',
        'b_date' => 'datetime',
        'order_qty' => 'decimal:2',
        'weight1' => 'decimal:2',
        'weight2' => 'decimal:2',
        'net_qty' => 'decimal:2',
        'density' => 'decimal:2',
        'purity' => 'decimal:2',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(I1BusinessCustomer::class, 'i1_customer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(I1Supplier::class, 'i1_supplier_id');
    }

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(I1Forwarder::class, 'i1_forwarder_id');
    }

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(I1Site::class, 'i1_source_site_id');
    }

    public function destSite(): BelongsTo
    {
        return $this->belongsTo(I1Site::class, 'i1_dest_site_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(I1Product::class, 'i1_product_id');
    }

    public function storageFrom(): BelongsTo
    {
        return $this->belongsTo(I1Storage::class, 'i1_storage_from_id');
    }

    public function storageTo(): BelongsTo
    {
        return $this->belongsTo(I1Storage::class, 'i1_storage_to_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(I1Status::class, 'i1_status_id');
    }

    public static function validationRules(): array
    {
        return [
            'tpt_order_nr' => 'required|string|max:16|unique:i1_orders,tpt_order_nr',
            'cust_order_nr' => 'required|string|max:16|unique:i1_orders,cust_order_nr',
            'i1_customer_id' => 'nullable|integer|exists:i1_business_customers,id',
            'i1_supplier_id' => 'nullable|integer|exists:i1_suppliers,id',
            'r_date' => 'nullable|date',
            'e_date' => 'nullable|date',
            'a_date' => 'nullable|date',
            'b_date' => 'nullable|date',
            'i1_forwarder_id' => 'required|integer|exists:i1_forwarders,id',
            'i1_source_site_id' => 'required|integer|exists:i1_sites,id',
            'i1_dest_site_id' => 'required|integer|exists:i1_sites,id',
            'i1_product_id' => 'required|integer|exists:i1_products,id',
            'order_qty' => 'nullable|numeric|min:0',
            'i1_storage_from_id' => 'nullable|integer|exists:i1_storage,id',
            'i1_storage_to_id' => 'nullable|integer|exists:i1_storage,id',
            'weight1' => 'nullable|numeric|min:0',
            'weight2' => 'nullable|numeric|min:0',
            'net_qty' => 'nullable|numeric|min:0',
            'density' => 'nullable|numeric|min:0',
            'purity' => 'nullable|numeric|min:0|max:100',
            'docs_array' => 'nullable|string|max:255',
            'analyse_array' => 'nullable|string|max:255',
            'i1_status_id' => 'required|integer|exists:i1_statuses,id',
            'comment' => 'nullable|string',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    public static function getSearchableColumns(): array
    {
        return [
            'tpt_order_nr',
            'cust_order_nr',
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
            'tpt_order_nr' => [
                'type' => 'input',
                'label' => 'TPT Order Number',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter TPT order number',
                'required' => true
            ],

            'cust_order_nr' => [
                'type' => 'input',
                'label' => 'Customer Order Number',
                'col_span' => 4,
                'rows' => 1,
                'placeholder' => 'Enter customer order number',
                'required' => true
            ],

            'i1_customer_id' => [
                'type' => 'select',
                'label' => 'Customer',
                'col_span' => 4,
                'options' => self::getRelationOptions('customer'),
                'help' => 'Select the customer for this order'
            ],

            'i1_supplier_id' => [
                'type' => 'select',
                'label' => 'Supplier',
                'col_span' => 4,
                'options' => self::getRelationOptions('supplier'),
                'help' => 'Select the supplier for this order'
            ],

            'r_date' => [
                'type' => 'datetime',
                'label' => 'Request Date',
                'col_span' => 2,
                'help' => 'Date when order was requested'
            ],

            'e_date' => [
                'type' => 'datetime',
                'label' => 'Expected Date',
                'col_span' => 2,
                'help' => 'Expected delivery date'
            ],

            'a_date' => [
                'type' => 'datetime',
                'label' => 'Actual Date',
                'col_span' => 2,
                'help' => 'Actual delivery date'
            ],

            'b_date' => [
                'type' => 'datetime',
                'label' => 'Booking Date',
                'col_span' => 2,
                'help' => 'Date when order was booked'
            ],

            'i1_forwarder_id' => [
                'type' => 'select',
                'label' => 'Forwarder',
                'col_span' => 4,
                'options' => self::getRelationOptions('forwarder'),
                'help' => 'Select the transport forwarder'
            ],

            'i1_source_site_id' => [
                'type' => 'select',
                'label' => 'Source Site',
                'col_span' => 4,
                'options' => self::getRelationOptions('sourceSite'),
                'help' => 'Select the source location'
            ],

            'i1_dest_site_id' => [
                'type' => 'select',
                'label' => 'Destination Site',
                'col_span' => 4,
                'options' => self::getRelationOptions('destSite'),
                'help' => 'Select the destination location'
            ],

            'i1_product_id' => [
                'type' => 'select',
                'label' => 'Product',
                'col_span' => 4,
                'options' => self::getRelationOptions('product'),
                'help' => 'Select the product for this order'
            ],

            'order_qty' => [
                'type' => 'number',
                'label' => 'Order Quantity',
                'col_span' => 2,
                'step' => '0.01',
                'help' => 'Quantity ordered'
            ],

            'i1_storage_from_id' => [
                'type' => 'select',
                'label' => 'Storage From',
                'col_span' => 4,
                'options' => self::getRelationOptions('storageFrom'),
                'help' => 'Select source storage location'
            ],

            'i1_storage_to_id' => [
                'type' => 'select',
                'label' => 'Storage To',
                'col_span' => 4,
                'options' => self::getRelationOptions('storageTo'),
                'help' => 'Select destination storage location'
            ],

            'weight1' => [
                'type' => 'number',
                'label' => 'Weight 1',
                'col_span' => 2,
                'step' => '0.01',
                'help' => 'First weight measurement'
            ],

            'weight2' => [
                'type' => 'number',
                'label' => 'Weight 2',
                'col_span' => 2,
                'step' => '0.01',
                'help' => 'Second weight measurement'
            ],

            'net_qty' => [
                'type' => 'number',
                'label' => 'Net Quantity',
                'col_span' => 2,
                'step' => '0.01',
                'help' => 'Net quantity delivered'
            ],

            'density' => [
                'type' => 'number',
                'label' => 'Density',
                'col_span' => 2,
                'step' => '0.01',
                'help' => 'Product density'
            ],

            'purity' => [
                'type' => 'number',
                'label' => 'Purity (%)',
                'col_span' => 2,
                'step' => '0.01',
                'min' => '0',
                'max' => '100',
                'help' => 'Product purity percentage'
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

            'i1_status_id' => [
                'type' => 'select',
                'label' => 'Status',
                'col_span' => 4,
                'options' => self::getRelationOptions('status'),
                'help' => 'Select the order status'
            ],

            'comment' => [
                'type' => 'textarea',
                'label' => 'Comment',
                'col_span' => 8,
                'rows' => 3,
                'help' => 'Additional comments about this order'
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
                'key' => 'tpt_order_nr',
                'label' => 'TPT Order',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'text',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'cust_order_nr',
                'label' => 'Customer Order',
                'type' => 'text',
                'width' => 'w-32',
                'formatter' => 'text',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'i1_customer_id',
                'label' => 'Customer',
                'type' => 'relation',
                'width' => 'w-40',
                'formatter' => 'text',
                'relationField' => 'name',
                'relation' => 'customer',
                'sortable' => true
            ],
            [
                'key' => 'i1_product_id',
                'label' => 'Product',
                'type' => 'relation',
                'width' => 'w-32',
                'formatter' => 'text',
                'relationField' => 'name',
                'relation' => 'product',
                'sortable' => true
            ],
            [
                'key' => 'order_qty',
                'label' => 'Quantity',
                'type' => 'number',
                'width' => 'w-24',
                'formatter' => 'number',
                'sortable' => true
            ],
            [
                'key' => 'i1_status_id',
                'label' => 'Status',
                'type' => 'relation',
                'width' => 'w-28',
                'formatter' => 'status',
                'relationField' => 'name',
                'relation' => 'status',
                'sortable' => true
            ],
            [
                'key' => 'r_date',
                'label' => 'Request Date',
                'type' => 'datetime',
                'width' => 'w-32',
                'formatter' => 'date',
                'sortable' => true
            ],
            [
                'key' => 'e_date',
                'label' => 'Expected Date',
                'type' => 'datetime',
                'width' => 'w-32',
                'formatter' => 'date',
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
            '1' => 'Pending',
            '2' => 'Released',
            '3' => 'Finished',
            '4' => 'On Hold',
            '5' => 'History'
        ];
    }

    public static function getContentFields(): array
    {
        return [
            'tpt_order_nr',
            'cust_order_nr',
            'i1_product_id',
            'order_qty',
            'comment', 
            'is_active',
            'is_locked',
        ];
    }

    public static function getRelationOptions(string $relation): array
    {
        $options = [];
        
        switch ($relation) {
            case 'customer':
                $customers = I1BusinessCustomer::where('is_active', true)->get();
                foreach ($customers as $customer) {
                    $options[$customer->id] = $customer->name ?? $customer->identifier;
                }
                break;
                
            case 'supplier':
                $suppliers = I1Supplier::where('is_active', true)->get();
                foreach ($suppliers as $supplier) {
                    $options[$supplier->id] = $supplier->name ?? $supplier->identifier;
                }
                break;
                
            case 'forwarder':
                $forwarders = I1Forwarder::where('is_active', true)->get();
                foreach ($forwarders as $forwarder) {
                    $options[$forwarder->id] = $forwarder->name ?? $forwarder->identifier;
                }
                break;
                
            case 'sourceSite':
            case 'destSite':
                $sites = I1Site::where('is_active', true)->get();
                foreach ($sites as $site) {
                    $options[$site->id] = $site->name ?? $site->identifier;
                }
                break;
                
            case 'product':
                $products = I1Product::where('is_active', true)->get();
                foreach ($products as $product) {
                    $options[$product->id] = $product->name ?? $product->identifier;
                }
                break;
                
            case 'storageFrom':
            case 'storageTo':
                $storage = I1Storage::where('is_active', true)->get();
                foreach ($storage as $item) {
                    $options[$item->id] = $item->identifier;
                }
                break;
                
            case 'status':
                $statuses = I1Status::where('is_active', true)->get();
                foreach ($statuses as $status) {
                    $options[$status->id] = $status->name;
                }
                break;
        }
        
        return $options;
    }

    public static function getPermissionAccess(): array
    {
        return [
            'globalRead' => [
                'description' => 'Full access to read all orders',
                'conditions' => [] // No conditions means full access
            ],
            'canReadUnlocked' => [
                'description' => 'Access to read all unlocked orders',
                'conditions' => [
                    'respect_lock' => true
                ]
            ],
            'canReadByStatus' => [
                'description' => 'Access to read orders by status',
                'conditions' => [
                    'respect_lock' => true,
                    'status_allowed' => [1, 2, 3] // Using status IDs
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read own orders only',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }

    public static function getTitleColumn(): string
    {
        return 'tpt_order_nr';
    }

    public static function getUserIdColumn(): string
    {
        return 'i1_user_id';
    }

} 