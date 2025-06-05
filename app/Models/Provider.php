<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'name',
        'type',
        'config',
    ];

    protected $casts = [
        'config' => 'array', // Automatically encodes/decodes JSON to/from array
    ];
}