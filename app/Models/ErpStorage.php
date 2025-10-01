<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpStorage extends Model
{
    use HasFactory;

    protected $table = 'erp_storages';

    protected $fillable = [
        'code',
        'type',
        'site_id',
        'unit_id',
        'max_units',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'json' => 'array',
        'max_units' => 'decimal:4'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function site()
    {
        return $this->belongsTo(ErpSite::class, 'site_id');
    }

    public function unit()
    {
        return $this->belongsTo(ErpUnit::class, 'unit_id');
    }

    public function stocks()
    {
        return $this->hasMany(ErpStock::class, 'storage_id');
    }

    // Table configuration methods (following ErpProduct pattern)
    
    public static function formFields()
    {
        return [
            'code' => [
                'type' => 'text',
                'label' => 'Storage Code',
                'required' => true,
                'placeholder' => 'Enter storage code'
            ],
            'type' => [
                'type' => 'select',
                'label' => 'Storage Type',
                'required' => true,
                'options' => [
                    'Warehouse' => 'Warehouse',
                    'Cold Storage' => 'Cold Storage',
                    'Dry Storage' => 'Dry Storage',
                    'Freezer' => 'Freezer'
                ]
            ],
            'site_id' => [
                'type' => 'select',
                'label' => 'Site',
                'required' => true,
                'source' => 'erp_sites',
                'field' => 'name'
            ],
            'unit_id' => [
                'type' => 'select',
                'label' => 'Unit',
                'required' => true,
                'source' => 'erp_units',
                'field' => 'name'
            ],
            'max_units' => [
                'type' => 'number',
                'label' => 'Max Units',
                'required' => true,
                'step' => 0.01,
                'placeholder' => 'Enter maximum units'
            ],
            'json' => [
                'type' => 'json',
                'label' => 'Additional Properties',
                'required' => false
            ],
            'comment' => [
                'type' => 'textarea',
                'label' => 'Description',
                'required' => false,
                'placeholder' => 'Enter storage comment'
            ],
            'is_active' => [
                'type' => 'checkbox',
                'label' => 'Active',
                'required' => false,
                'default' => true
            ],
            'is_locked' => [
                'type' => 'checkbox',
                'label' => 'Locked',
                'required' => false,
                'default' => false
            ]
        ];
    }

    public static function validationRules()
    {
        return [
            'code' => 'required|string|max:255|unique:erp_storages,code',
            'type' => 'required|string|max:255|in:Warehouse,Cold Storage,Dry Storage,Freezer',
            'site_id' => 'required|exists:erp_sites,id',
            'unit_id' => 'required|exists:erp_units,id',
            'max_units' => 'required|numeric|min:0|max:999999.99',
            'json' => 'nullable|array',
            'comment' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_locked' => 'boolean'
        ];
    }

    public static function getSearchableColumns()
    {
        return [
            'code',
            'type',
            'comment',
        ];
    }

    /**
     * Get searchable fields including relationship fields for enhanced datatable
     */
    public static function getSearchableFields()
    {
        return [
            'code',
            'type', 
            'comment',
            'site.name',
            'unit.name'
        ];
    }

    public function linksTable()
    {
        return [
            'View Details' => '/storages/{id}',
            'Edit Storage' => '/storages/{id}/edit',
            'Stock Levels' => '/inventory/stocks?storage={id}',
            'Usage History' => '/reports/storage-usage?storage={id}',
        ];
    }

    public function getDataTableRelationships()
    {
        return [
            'site' => [
                'table' => 'erp_sites',
                'foreignKey' => 'site_id',
                'displayField' => 'name'
            ],
            'unit' => [
                'table' => 'erp_units', 
                'foreignKey' => 'unit_id',
                'displayField' => 'name'
            ]
        ];
    }

    // Specify which table this model handles
    public function getTableName()
    {
        return 'erp_storages'; // This model handles the 'erp_storages' table
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
            'table' => 'erp_storages',
            'title' => 'Storages',
            'columns' => [
                ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px'],
                ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'searchable' => true, 'width' => '120px'],
                ['key' => 'type', 'label' => 'Type', 'sortable' => true, 'searchable' => true, 'width' => '120px'],
                ['key' => 'site.name', 'label' => 'Site', 'sortable' => true, 'searchable' => true],
                ['key' => 'unit.name', 'label' => 'Unit', 'sortable' => true, 'width' => '100px'],
                ['key' => 'max_units', 'label' => 'Max Units', 'sortable' => true, 'width' => '100px'],
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
     * Get API relationships for eager loading
     */
    public static function getApiRelationships()
    {
        return ['site', 'unit'];
    }

    /**
     * Get KPI configuration for enhanced datatable
     */
    public static function getKpiConfig()
    {
        return [
            [
                'key' => 'total',
                'label' => 'Total Storages',
                'icon' => 'fas fa-warehouse'
            ],
            [
                'key' => 'active',
                'label' => 'Active Storages',
                'icon' => 'fas fa-check-circle'
            ],
            [
                'key' => 'locked',
                'label' => 'Locked Storages',
                'icon' => 'fas fa-lock'
            ],
            [
                'key' => 'sites',
                'label' => 'Sites',
                'icon' => 'fas fa-building'
            ],
            [
                'key' => 'types',
                'label' => 'Storage Types',
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
                'key' => 'type',
                'label' => 'Storage Type',
                'type' => 'select',
                'source' => 'erp_storages',
                'field' => 'type',
                'relationship' => null
            ],
            [
                'key' => 'site',
                'label' => 'Site',
                'type' => 'select',
                'source' => 'erp_sites',
                'field' => 'name',
                'relationship' => 'site'
            ],
            [
                'key' => 'unit',
                'label' => 'Unit',
                'type' => 'select',
                'source' => 'erp_units',
                'field' => 'name',
                'relationship' => 'unit'
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
                    'key' => 'code',
                    'label' => 'Storage Code',
                    'type' => 'text',
                    'required' => true,
                    'validation' => 'required|string|max:255'
                ],
                [
                    'key' => 'type',
                    'label' => 'Storage Type',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Warehouse' => 'Warehouse',
                        'Cold Storage' => 'Cold Storage',
                        'Dry Storage' => 'Dry Storage',
                        'Freezer' => 'Freezer'
                    ],
                    'validation' => 'required|string|in:Warehouse,Cold Storage,Dry Storage,Freezer'
                ],
                [
                    'key' => 'site_id',
                    'label' => 'Site',
                    'type' => 'select',
                    'source' => 'erp_sites',
                    'required' => true,
                    'validation' => 'required|exists:erp_sites,id'
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
                    'key' => 'max_units',
                    'label' => 'Max Units',
                    'type' => 'number',
                    'required' => true,
                    'step' => 0.01,
                    'validation' => 'required|numeric|min:0|max:999999.99'
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
                'label' => 'Add Storage',
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
     * Get filter dependencies for cascading filters
     */
    public static function getFilterDependencies()
    {
        return [
            [
                'filterKey' => 'type',
                'dependsOn' => [],
                'resets' => ['site', 'unit']
            ],
            [
                'filterKey' => 'site',
                'dependsOn' => ['type'],
                'resets' => ['unit']
            ],
            [
                'filterKey' => 'unit',
                'dependsOn' => ['type', 'site'],
                'resets' => []
            ]
        ];
    }

    /**
     * Get export fields configuration
     */
    public static function getExportFields()
    {
        return array_map(function($col) {
            return [
                'key' => $col['key'],
                'label' => $col['label'],
                'transform' => $col['key'] === 'is_active' ? function($value) { return $value ? 'Yes' : 'No'; } : null
            ];
        }, static::getDataTableConfig()['columns']);
    }

    /**
     * Get dropdowns configuration
     */
    public static function getDropdowns()
    {
        return [
            ['key' => 'sites', 'endpoint' => '/api/erp_sites'],
            ['key' => 'units', 'endpoint' => '/api/erp_units']
        ];
    }

    /**
     * Get form props configuration
     */
    public static function getFormProps()
    {
        return [
            'sites' => 'sites',
            'units' => 'units'
        ];
    }

    /**
     * Get API headers configuration
     */
    public static function getApiHeaders()
    {
        return [
            'X-From-Storages-Page' => 'true'
        ];
    }

    /**
     * Get default values for new records
     */
    public static function getDefaultValues()
    {
        return [
            'is_active' => true,
            'is_locked' => false,
            'max_units' => 0
        ];
    }

    /**
     * Get display name for storage
     */
    public static function getDisplayName($storage)
    {
        return $storage->code . ' - ' . $storage->type;
    }

    /**
     * Get status configuration for badges
     */
    public static function getStatusConfig()
    {
        return [
            'is_active' => [
                'true' => ['label' => 'Active', 'class' => 'bg-green-100 text-green-800'],
                'false' => ['label' => 'Inactive', 'class' => 'bg-red-100 text-red-800']
            ],
            'is_locked' => [
                'true' => ['label' => 'Locked', 'class' => 'bg-yellow-100 text-yellow-800'],
                'false' => ['label' => 'Unlocked', 'class' => 'bg-gray-100 text-gray-800']
            ]
        ];
    }

    /**
     * Get sort configuration
     */
    public static function getSortConfig()
    {
        return [
            'default' => 'code',
            'direction' => 'asc',
            'options' => [
                'code' => 'Storage Code',
                'type' => 'Storage Type',
                'site.name' => 'Site',
                'max_units' => 'Max Units',
                'created_at' => 'Created Date'
            ]
        ];
    }

    /**
     * Get pagination configuration
     */
    public static function getPaginationConfig()
    {
        return [
            'perPage' => 20,
            'perPageOptions' => [10, 20, 50, 100],
            'showPerPage' => true,
            'showTotal' => true
        ];
    }

    /**
     * Get complete enhanced datatable configuration
     */
    public static function getEnhancedDataTableConfig()
    {
        return [
            'title' => 'Storages',
            'icon' => 'fas fa-warehouse',
            'apiEndpoint' => '/api/erp_storages',
            'tableName' => 'erp_storages',
            'searchFields' => static::getSearchableFields(),
            'kpis' => static::getKpiConfig(),
            'filters' => static::getFilterConfig(),
            'form' => static::getFormConfig(),
            'actions' => static::getActionConfig(),
            'columns' => static::getDataTableConfig()['columns'],
            'relationships' => static::getApiRelationships(),
            'filterDependencies' => static::getFilterDependencies(),
            'exportFields' => static::getExportFields(),
            'dropdowns' => static::getDropdowns(),
            'formComponent' => 'StorageForm',
            'formProps' => static::getFormProps(),
            'apiHeaders' => static::getApiHeaders(),
            'defaultValues' => static::getDefaultValues(),
            'statusConfig' => static::getStatusConfig(),
            'sortConfig' => static::getSortConfig(),
            'paginationConfig' => static::getPaginationConfig()
        ];
    }
}