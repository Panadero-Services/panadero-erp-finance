<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'action', 'module', 'node', 'team', 'project', 'content', 'json', 'tags', 'user_id'
    ];

    public static function criteria() {
        return [
            'action' => 'required|string|max:128',
            'module' => 'required|string|max:128',
            'node' => 'required|string|max:128',
            'team' => 'nullable|string|max:128',
            'project' => 'nullable|string|max:128',
            'content' => 'nullable|string',
            'json' => 'nullable|string',
            'tags' => 'nullable|string',
            'user_id' => 'nullable|integer',
        ];
    }
    
}
