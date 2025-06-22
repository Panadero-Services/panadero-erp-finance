<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableConfig extends Model
{
    protected $fillable = [
        'module',
        'table',
        'model',
        'title',
        'description',
        'icon',
        'color',
        'per_page_default',
        'view_mode_default',
        'searchable',
        'editable',
        'deletable',
        'config'
    ];

    protected $casts = [
        'config' => 'array',
        'searchable' => 'boolean',
        'editable' => 'boolean',
        'deletable' => 'boolean'
    ];

    public static function getConfig($module, $table)
    {
        return static::where('module', $module)
                    ->where('table', $table)
                    ->first();
    }
} 