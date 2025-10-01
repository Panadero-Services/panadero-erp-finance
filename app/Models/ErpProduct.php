<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialIdentifier;

class ErpProduct extends Model
{
    use HasFactory, HasSequentialIdentifier;

    protected $table = 'erp_products';

    protected $fillable = [
        'name',
        'identifier',
        'erp_product_type_id',
        'erp_product_group_id',
        'erp_brand_id',
        'unit_id',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'json' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('erp_product_type_id', $typeId);
    }

    public function unit()
    {
        return $this->belongsTo(ErpUnit::class, 'unit_id');
    }

    public function stocks()
    {
        return $this->hasMany(ErpStock::class, 'product_id');
    }

    public function orderInLines()
    {
        return $this->hasMany(ErpOrderInLine::class, 'product_id');
    }

    public function orderOutLines()
    {
        return $this->hasMany(ErpOrderOutLine::class, 'product_id');
    }

    public function productType()
    {
        return $this->belongsTo(ErpProductType::class, 'erp_product_type_id');
    }

    public function productGroup()
    {
        return $this->belongsTo(ErpProductGroup::class, 'erp_product_group_id');
    }

    public function brand()
    {
        return $this->belongsTo(ErpBrand::class, 'erp_brand_id');
    }

    // Table configuration methods (following Feature.php pattern)
    
    public function formFields()
    {
        return [
            'name' => ['type' => 'text', 'label' => 'Product Name', 'required' => true],
            'identifier' => ['type' => 'text', 'label' => 'SKU/Identifier', 'required' => true],
            'product_type_id' => ['type' => 'select', 'label' => 'Product Type', 'required' => true, 'options' => 'productTypes'],
            'product_group_id' => ['type' => 'select', 'label' => 'Product Group', 'required' => false, 'options' => 'productGroups'],
            'erp_brand_id' => ['type' => 'select', 'label' => 'Brand', 'required' => false, 'options' => 'brands'],
            'unit_id' => ['type' => 'select', 'label' => 'Unit', 'required' => true, 'options' => 'units'],
            'json' => ['type' => 'json', 'label' => 'Additional Properties', 'required' => false],
            'comment' => ['type' => 'textarea', 'label' => 'Description', 'required' => false],
            'is_active' => ['type' => 'checkbox', 'label' => 'Active', 'required' => false],
            'is_locked' => ['type' => 'checkbox', 'label' => 'Locked', 'required' => false],
        ];
    }

    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:16|unique:erp_products,identifier', // Changed to nullable
            'erp_product_type_id' => 'required|exists:erp_product_types,id',
            'erp_product_group_id' => 'nullable|exists:erp_product_groups,id',
            'erp_brand_id' => 'nullable|exists:erp_brands,id',
            'unit_id' => 'required|exists:erp_units,id',
            'comment' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_locked' => 'boolean'
        ];
    }

    public function getSearchableColumns()
    {
        return [
            'name',
            'identifier',
            'comment',
        ];
    }

    /**
     * Get searchable fields including relationship fields for enhanced datatable
     */
    public static function getSearchableFields()
    {
        return [
            'name',
            'identifier', 
            'comment',
            'product_group.name',
            'product_type.name',
            'brand.name'
        ];
    }

    public function linksTable()
    {
        return [
            'View Details' => '/products/{id}',
            'Edit Product' => '/products/{id}/edit',
            'Stock Levels' => '/inventory/stocks?product={id}',
            'Order History' => '/orders?product={id}',
        ];
    }

    public function getDataTableRelationships()
    {
        return [
            'brand' => [
                'table' => 'erp_brand',
                'foreignKey' => 'erp_brand_id',
                'displayField' => 'name'
            ],
            'productType' => [
                'table' => 'erp_product_type', 
                'foreignKey' => 'erp_product_type_id',
                'displayField' => 'name'
            ],
            'productGroup' => [
                'table' => 'erp_product_group',
                'foreignKey' => 'erp_product_group_id', 
                'displayField' => 'name'
            ],
            'unit' => [
                'table' => 'erp_unit',
                'foreignKey' => 'unit_id',
                'displayField' => 'name'
            ]
        ];
    }

    // Specify which table this model handles
    public function getTableName()
    {
        return 'erp_products'; // This model handles the 'erp_products' table
    }

    public function canBeEditedBy($user)
{
    // For now, allow all authenticated users to edit
    return true;
}

    // Generate complete DataTable configuration from model
    public static function getDataTableConfig()
    {
        return [
            'table' => 'erp_products',
            'title' => 'Products',
            'columns' => [
                ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px'],
                ['key' => 'identifier', 'label' => 'SKU', 'sortable' => true, 'searchable' => true, 'width' => '120px'],
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
                ['key' => 'product_group.name', 'label' => 'Group', 'sortable' => true, 'searchable' => true],
                ['key' => 'product_type.name', 'label' => 'Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'brand.name', 'label' => 'Brand', 'sortable' => true, 'searchable' => true],
                ['key' => 'unit.name', 'label' => 'Unit', 'sortable' => true, 'width' => '80px'],
                [
                    'key' => 'is_active',
                    'label' => 'Status',
                    'sortable' => true,
                    'type' => 'badge',
                    'width' => '100px',
                    'badgeColors' => [
                        'true' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'false' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    ]
                ],
                ['key' => 'created_at', 'label' => 'Created At', 'sortable' => true, 'type' => 'date']
            ],
            'itemsPerPage' => 20,
            'variant' => 'default',
            'showCreate' => true,
            'showEdit' => true,
            'showDelete' => true,
            'showBulkActions' => true,
            'showSearch' => false // Disable search bar since we use external filters
        ];
    }

    /**
     * Generate identifier with brand, group, and sequential number
     */
    public static function generateProductIdentifier($brandId = null, $productGroupId = null)
    {
        // Get brand identifier (3 chars)
        $brand = $brandId ? \App\Models\ErpBrand::find($brandId) : null;
        $brandCode = $brand ? $brand->identifier : 'XXX';
        $brandPart = strtoupper(substr($brandCode, 0, 3)) . str_repeat('X', max(0, 3 - strlen($brandCode)));
        
        // Get product group code (3 chars)
        $productGroup = $productGroupId ? \App\Models\ErpProductGroup::find($productGroupId) : null;
        $groupCode = $productGroup ? $productGroup->code : 'XXX';
        $groupPart = strtoupper(substr($groupCode, 0, 3)) . str_repeat('X', max(0, 3 - strlen($groupCode)));
        
        // Generate unique 5-digit number using the existing trait
        $uniquePart = static::getNextNumber($brandPart . $groupPart, 5);
        
        return $brandPart . $groupPart . $uniquePart;
    }

    /**
     * Get API relationships for eager loading
     */
    public static function getApiRelationships()
    {
        return ['productType', 'productGroup', 'brand', 'unit'];
    }

    /**
     * Get KPI configuration for enhanced datatable
     */
    public static function getKpiConfig()
    {
        return [
            [
                'key' => 'total',
                'label' => 'Total Products',
                'icon' => 'fas fa-boxes'
            ],
            [
                'key' => 'active',
                'label' => 'Active Products',
                'icon' => 'fas fa-check-circle'
            ],
            [
                'key' => 'locked',
                'label' => 'Locked Products',
                'icon' => 'fas fa-lock'
            ],
            [
                'key' => 'groups',
                'label' => 'Product Groups',
                'icon' => 'fas fa-layer-group'
            ],
            [
                'key' => 'brands',
                'label' => 'Brands',
                'icon' => 'fas fa-tags'
            ],
            [
                'key' => 'types',
                'label' => 'Product Types',
                'icon' => 'fas fa-cube'
            ]
        ];
    }

    /**
     * Get filter configuration for enhanced datatable
     */
    public static function getFilterConfig()
    {
        return [
            [
                'key' => 'product_type',
                'label' => 'Product Type',
                'type' => 'select',
                'source' => 'erp_product_types',
                'field' => 'name',
                'relationship' => 'productType'
            ],
            [
                'key' => 'product_group',
                'label' => 'Product Group',
                'type' => 'select',
                'source' => 'erp_product_groups',
                'field' => 'name',
                'relationship' => 'productGroup'
            ],
            [
                'key' => 'brand',
                'label' => 'Brand',
                'type' => 'select',
                'source' => 'erp_brands',
                'field' => 'name',
                'relationship' => 'brand'
            ]
        ];
    }

    /**
     * Get form configuration for enhanced datatable
     */
    public static function getFormConfig()
    {
        return [
            'fields' => [
                [
                    'key' => 'name',
                    'label' => 'Product Name',
                    'type' => 'text',
                    'required' => true,
                    'validation' => 'required|string|max:255'
                ],
                [
                    'key' => 'identifier',
                    'label' => 'SKU/Identifier',
                    'type' => 'text',
                    'disabled' => true,
                    'placeholder' => 'Will be auto-generated'
                ],
                [
                    'key' => 'erp_product_type_id',
                    'label' => 'Product Type',
                    'type' => 'select',
                    'source' => 'erp_product_types',
                    'required' => true,
                    'validation' => 'required|exists:erp_product_types,id'
                ],
                [
                    'key' => 'erp_product_group_id',
                    'label' => 'Product Group',
                    'type' => 'select',
                    'source' => 'erp_product_groups',
                    'validation' => 'nullable|exists:erp_product_groups,id'
                ],
                [
                    'key' => 'erp_brand_id',
                    'label' => 'Brand',
                    'type' => 'select',
                    'source' => 'erp_brands',
                    'validation' => 'nullable|exists:erp_brands,id'
                ],
                [
                    'key' => 'unit_id',
                    'label' => 'Unit',
                    'type' => 'select',
                    'source' => 'erp_units',
                    'required' => true,
                    'validation' => 'required|exists:erp_units,id'
                ],
                [
                    'key' => 'comment',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'validation' => 'nullable|string|max:1000'
                ],
                [
                    'key' => 'is_active',
                    'label' => 'Active',
                    'type' => 'checkbox',
                    'default' => true
                ],
                [
                    'key' => 'is_locked',
                    'label' => 'Locked',
                    'type' => 'checkbox',
                    'default' => false
                ]
            ]
        ];
    }

    /**
     * Get action configuration for enhanced datatable
     */
    public static function getActionConfig()
    {
        return [
            [
                'key' => 'create',
                'label' => 'Add Product',
                'icon' => 'fas fa-plus',
                'variant' => 'primary'
            ],
            [
                'key' => 'export',
                'label' => 'Export',
                'icon' => 'fas fa-download',
                'variant' => 'success'
            ],
            [
                'key' => 'export-raw',
                'label' => 'Export Raw Data',
                'icon' => 'fas fa-database',
                'variant' => 'warning'
            ],
            [
                'key' => 'import',
                'label' => 'Import',
                'icon' => 'fas fa-upload',
                'variant' => 'info'
            ]
        ];
    }

    /**
     * Get complete enhanced datatable configuration
     */
    public static function getEnhancedDataTableConfig()
    {
        return [
            'title' => 'Products',
            'icon' => 'fas fa-box',
            'apiEndpoint' => '/api/erp_products',
            'tableName' => 'erp_products',
            'searchFields' => static::getSearchableFields(),
            'kpis' => static::getKpiConfig(),
            'filters' => static::getFilterConfig(),
            'form' => static::getFormConfig(),
            'actions' => static::getActionConfig(),
            'columns' => static::getDataTableConfig()['columns'],
            'relationships' => static::getApiRelationships()
        ];
    }
}