<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
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
    ];

    public function getTable()
    {
        return 'features';
    }

    public function formFields()
    {
        return [
            'item' => ['type' => 'text', 'label' => 'Item'],
            'title' => ['type' => 'text', 'label' => 'Title'],
            'description' => ['type' => 'textarea', 'label' => 'Description'],
            'color' => ['type' => 'text', 'label' => 'Color'],
            'version' => ['type' => 'text', 'label' => 'Version'],
            'icon' => ['type' => 'text', 'label' => 'Icon'],
            'options' => ['type' => 'json', 'label' => 'Options'],
            'links' => ['type' => 'json', 'label' => 'Links'],
            'path' => ['type' => 'text', 'label' => 'Path'],
            'status' => ['type' => 'text', 'label' => 'Status'],
            'progress' => ['type' => 'number', 'label' => 'Progress'],
            'is_active' => ['type' => 'checkbox', 'label' => 'Active'],
        ];
    }

    public function validationRules()
    {
        return [
            'item' => 'required|string|max:255|unique:features,item,' . $this->id,
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'color' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'links' => 'nullable|array',
            'path' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'progress' => 'required|integer|min:0|max:100',
            'is_active' => 'required|boolean',
        ];
    }

    public function getSearchableColumns()
    {
        return [
            'item',
            'title',
            'description',
            'status',
        ];
    }

    public function linksTable()
    {
        return [
            'Demo' => '/home/dashboard',
            'Grid sample' => '/grid',
        ];
    }
} 