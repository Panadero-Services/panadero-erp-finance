<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Traits\HasPermissions;

class BusinessService extends Model
{
    use HasFactory, HasPermissions;

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
        return ['item', 'title', 'description', 'color', 'status'];
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
                'width' => 'w-24',
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
                'width' => 'w-48',
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
                'width' => 'w-24',
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
                'width' => 'w-16',
                'formatter' => 'boolean'
            ],
            [
                'key' => 'created_at',
                'label' => 'Created',
                'type' => 'date',
                'width' => 'w-24',
                'formatter' => 'date'
            ],
            [
                'key' => 'updated_at',
                'label' => 'Updated',
                'type' => 'date',
                'width' => 'w-24',
                'formatter' => 'date'
            ],
            [
                'key' => 'options',
                'label' => 'Options',
                'type' => 'json',
                'width' => 'w-24',
                'formatter' => 'json'
            ],
            [
                'key' => 'links',
                'label' => 'Links',
                'type' => 'json',
                'width' => 'w-24',
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
            'pending' => 'yellow',
            'deprecated' => 'red',
            'maintenance' => 'orange',
            'in_progress' => 'blue',
            'testing' => 'yellow',
            'ready' => 'green',
            'experimental' => 'cyan',
            'alpha' => 'pink',
            'beta' => 'purple',
            'featured' => 'purple'
        ];
    }

    public static function getColorOptions(): array
    {
        return [
            // Primary Colors
            'blue' => 'Blue',
            'red' => 'Red',
            'green' => 'Green',
            'yellow' => 'Yellow',
            
            // Rich Colors
            'indigo' => 'Indigo',
            'purple' => 'Purple',
            'violet' => 'Violet',
            'fuchsia' => 'Fuchsia',
            'pink' => 'Pink',
            'rose' => 'Rose',
            
            // Nature Colors
            'emerald' => 'Emerald',
            'teal' => 'Teal',
            'cyan' => 'Cyan',
            'sky' => 'Sky',
            'lime' => 'Lime',
            'amber' => 'Amber',
            
            // Warm Colors
            'orange' => 'Orange',
            'amber' => 'Amber',
            'coral' => 'Coral',
            'crimson' => 'Crimson',
            'maroon' => 'Maroon',
            'sienna' => 'Sienna',
            
            // Cool Colors
            'azure' => 'Azure',
            'cerulean' => 'Cerulean',
            'sapphire' => 'Sapphire',
            'turquoise' => 'Turquoise',
            'aqua' => 'Aqua',
            
            // Earth Tones
            'brown' => 'Brown',
            'bronze' => 'Bronze',
            'copper' => 'Copper',
            'sepia' => 'Sepia',
            'umber' => 'Umber',
            
            // Monochrome
            'slate' => 'Slate',
            'zinc' => 'Zinc',
            'gray' => 'Gray',
            'neutral' => 'Neutral',
            'stone' => 'Stone',
            
            // Jewel Tones
            'ruby' => 'Ruby',
            'amethyst' => 'Amethyst',
            'topaz' => 'Topaz',
            'jade' => 'Jade',
            'pearl' => 'Pearl'
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
                'type' => 'json',
                'label' => 'Options',
                'col_span' => 8,
                'help' => 'Use this to store service options in JSON format'
            ],
            'links' => [
                'type' => 'links',
                'label' => 'Links',
                'col_span' => 8,
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
            'description', 'version', 'icon', 'is_active', 'progress'
        ];
    }

  /**
     * Define permission-based access for futures
     * Override the trait's default implementation
     */
    public static function getPermissionAccess(): array
    {
        return [
            'canReadAll' => [
                'description' => 'Full access to read all futures',
                'conditions' => [] // No conditions means full access
            ],
            'canReadUnlocked' => [
                'description' => 'Access to read all unlocked futures',
                'conditions' => [
                    'respect_lock' => true
                ]
            ],
            'canReadByStatus' => [
                'description' => 'Access to read specific status futures',
                'conditions' => [
                    'status_allowed' => ['idle', 'in_progress', 'blocked']
                ]
            ],
            'canReadOwn' => [
                'description' => 'Access to read own futures only',
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true,
                    'status_allowed' => ['completed', 'review']
                ]
            ]
        ];
    }


    public static function getTitleColumn(): string
    {
        return 'title';
    }

    public static function getUserIdColumn(): string
    {
        return 'user_id';
    }


// For a model with color options
public static function optionsFormat(): array
{
    return [
        [
            'name' => 'name',
            'type' => 'text',
            'label' => ' Name',
            'required' => true
        ],
        [
            'name' => 'url',
            'type' => 'text',
            'label' => 'Url',
            'required' => true
        ],
        [
            'name' => 'route',
            'type' => 'text',
            'label' => 'Route',
            'required' => true
        ],
    ];
}


} 