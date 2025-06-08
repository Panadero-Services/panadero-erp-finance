<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'text_length',
    ];

    /**
     * Get the posts for this type.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
} 