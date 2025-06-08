<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'page_id',
        'file',
        'subtitle',
        'icon',
        'image',
        'slogan',
        'html',
        'css',
        'features',
        'settings',
        'self',
        'self_admin',
        'priority',
        'is_active',
        'self_auth',
        'animate'
    ];
}
