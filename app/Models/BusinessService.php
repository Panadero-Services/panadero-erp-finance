<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active' => 'boolean'
    ];

    public function getTable()
    {
        return 'business_services';
    }

    public function formFields()
    {
        return [
            'item' => 'text',
            'title' => 'text',
            'description' => 'textarea',
            'color' => 'text',
            'version' => 'text',
            'icon' => 'text',
            'options' => 'json',
            'links' => 'json',
            'path' => 'text',
            'status' => 'text',
            'progress' => 'number',
            'is_active' => 'checkbox'
        ];
    }

    public function validationRules()
    {
        return [
            'item' => 'required|string|max:8|unique:business_services,item,' . $this->id,
            'title' => 'required|string|max:32',
            'description' => 'required|string',
            'color' => 'required|string|max:32',
            'version' => 'required|string|max:32',
            'icon' => 'required|string|max:32',
            'options' => 'nullable|json',
            'links' => 'nullable|json',
            'path' => 'required|string|max:16',
            'status' => 'required|string|max:16',
            'progress' => 'required|integer',
            'is_active' => 'boolean'
        ];
    }

    public function getSearchableColumns()
    {
        return ['item', 'title', 'description', 'color', 'version', 'icon', 'path', 'status'];
    }

    public function linksTable()
    {
        return [
            'Dashboard' => '/home/dashboard',
            'Administration' => '/home/administration',
            'Welcome' => '/home/welkom',
            'Tiers' => '/home/tiers',
            'Sandbox' => '/home/sandbox'
        ];
    }
} 