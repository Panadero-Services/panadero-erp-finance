<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Generate complete DataTable configuration from model
    public function getDataTableConfig()
    {
        return [
            'table' => 'products',
            'title' => 'Products',
            'columns' => [
                ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px'],
                ['key' => 'sku', 'label' => 'SKU', 'sortable' => true, 'searchable' => true, 'width' => '120px'],
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
                ['key' => 'price', 'label' => 'Price', 'sortable' => true, 'type' => 'currency'],
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
            'showBulkActions' => true
        ];
    }

    public function getDataTableRelationships()
    {
        return [];
    }

    public function formFields()
    {
        return [
            'name' => ['label' => 'Product Name', 'type' => 'text', 'required' => true],
            'sku' => ['label' => 'SKU', 'type' => 'text', 'required' => true, 'unique' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'price' => ['label' => 'Price', 'type' => 'number', 'step' => '0.01'],
            'is_active' => ['label' => 'Active', 'type' => 'boolean', 'default' => true]
        ];
    }

    public function getSearchableColumns()
    {
        return ['name', 'sku', 'description'];
    }
}
