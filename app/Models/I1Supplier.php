<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class I1Supplier extends Model
{
    use HasFactory;

    protected $table = 'i1_suppliers';

    protected $fillable = [
        'identifier',
        'name',
        'address',
        'city',
        'postal_code',
        'i1_country_id',
        'email',
        'comment',
        'i1_user_id',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(I1Country::class, 'i1_country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'i1_user_id');
    }
} 