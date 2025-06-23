<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessService extends Model
{
    use HasFactory;

    protected $fillable = [
        'item',
        'title',
        'description',
        'color',
        'version',
        'icon',
        'options',
        'links',
        'path',
        'status',
        'progress',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'links' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static function validationRules(): array
    {
        return [
            'item' => 'required|string|max:8|unique:business_services,item,' . request()->route('id'),
            'title' => 'required|string|max:32',
            'description' => 'required|string',
            'color' => 'required|string|max:32',
            'version' => 'required|string|max:32',
            'icon' => 'required|string|max:32',
            'options' => 'nullable|array',
            'links' => 'nullable|array',
            'path' => 'required|string|max:16',
            'status' => 'required|string|max:16',
            'progress' => 'required|integer',
            'is_active' => 'boolean'
        ];
    }

    public static function getSearchableColumns(): array
    {
        return ['item', 'title', 'description', 'color', 'version', 'icon', 'path', 'status'];
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
                'key' => 'progress',
                'label' => 'Progress',
                'type' => 'number',
                'width' => 'w-24',
                'formatter' => 'text'
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
                'key' => 'options',
                'label' => 'Options',
                'type' => 'json',
                'width' => 'w-48',
                'formatter' => 'json'
            ],
            [
                'key' => 'links',
                'label' => 'Links',
                'type' => 'json',
                'width' => 'w-48',
                'formatter' => 'json'
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

    public static function formFields(): array
    {
        return [
            'item' => [
                'type' => 'text',
                'label' => 'Item',
                'col_span' => 4,
                'placeholder' => 'Enter service item',
                'required' => true
            ],
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'col_span' => 4,
                'placeholder' => 'Enter service title',
                'required' => true
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'col_span' => 8,
                'rows' => 2,
                'required' => true
            ],
            'color' => [
                'type' => 'select',
                'label' => 'Color',
                'col_span' => 2,
                'options' => self::getColorOptions(),
                'default' => 'blue'
            ],
            'version' => [
                'type' => 'text',
                'label' => 'Version',
                'col_span' => 2,
                'placeholder' => 'Enter version',
                'required' => true
            ],
            'icon' => [
                'type' => 'text',
                'label' => 'Icon',
                'col_span' => 2,
                'placeholder' => 'Enter icon',
                'required' => true
            ],
            'path' => [
                'type' => 'text',
                'label' => 'Path',
                'col_span' => 2,
                'placeholder' => 'Enter path',
                'required' => true
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col_span' => 2,
                'default' => 'idle',
                'required' => true
            ],
            'progress' => [
                'type' => 'number',
                'label' => 'Progress',
                'col_span' => 2,
                'placeholder' => 'Enter progress (0-100)',
                'required' => true
            ],
            'options' => [
                'type' => 'textarea',
                'label' => 'Options',
                'col_span' => 8,
                'rows' => 2,
                'help' => 'Use this to store service options in JSON format'
            ],
            'links' => [
                'type' => 'textarea',
                'label' => 'Links',
                'col_span' => 8,
                'rows' => 2,
                'help' => 'Link this service to other services with different relationships'
            ],
            'is_active' => [
                'type' => 'switch',
                'label' => 'Active',
                'col_span' => 1,
                'default' => true
            ]
        ];
    }

    public static function getContentFields(): array
    {
        return [
            'description', 'color', 'version', 'icon'
        ];
    }
} 