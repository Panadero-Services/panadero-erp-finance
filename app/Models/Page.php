<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'file', 'icon', 'image', 'slogan', 'type', 'settings', 'user_id', 'project_id', 'is_active', 'sidebar', 'header', 'footer', 'public', 'animate', 'max_width'
    ];

    public function sections(): hasMany {
        return $this->hasMany(Section::class)->orderBy('priority', 'asc');
    }
}
