<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'user_id',
        'description',
        'json',
        'is_active'
    ];

    /**
     * JaW says...:
     * 
     * Set Validation Rules HERE!!!!!
     *
     * @return array<string, string>
     */
    protected function validRules(): array
    {
        return [
            'title' => 'required|string|min:4',
            'title' => 'required|string|max:18'
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }


}
