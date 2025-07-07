<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class I1Storage extends Model
{
    use HasFactory;

    protected $table = 'i1_storage';

    protected $fillable = [
        'identifier',
        'i1_source_site_id',
        'i1_product_id',
        'max_qty',
        'actual_qty',
        'tag',
        'realtime_percentage',
        'realtime_update',
        'units',
        'actual_density',
        'actual_purity',
        'default_sequence',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'max_qty' => 'decimal:2',
        'actual_qty' => 'decimal:2',
        'realtime_percentage' => 'decimal:2',
        'realtime_update' => 'datetime',
        'actual_density' => 'decimal:2',
        'actual_purity' => 'decimal:2',
        'default_sequence' => 'integer',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(I1Site::class, 'i1_source_site_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(I1Product::class, 'i1_product_id');
    }
} 