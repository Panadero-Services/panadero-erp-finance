<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class I1Product extends Model
{
    use HasFactory;

    protected $table = 'i1_products';

    protected $fillable = [
        'identifier',
        'name',
        'i1_product_group_id',
        'purity',
        'density',
        'un_code',
        'un_nl',
        'un_fr',
        'un_uk',
        'un_de',
        'is_base_product',
        'i1_base_product_id',
        'i1_mix1_product_id',
        'i1_mix2_product_id',
        'i1_mix3_product_id',
        'i1_balance_product_id',
        'base_percentage',
        'mix1_percentage',
        'mix2_percentage',
        'mix3_percentage',
        'balance_percentage',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'purity' => 'decimal:2',
        'density' => 'decimal:2',
        'un_code' => 'integer',
        'is_base_product' => 'boolean',
        'base_percentage' => 'decimal:2',
        'mix1_percentage' => 'decimal:2',
        'mix2_percentage' => 'decimal:2',
        'mix3_percentage' => 'decimal:2',
        'balance_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(I1ProductGroup::class, 'i1_product_group_id');
    }

    public function baseProduct(): BelongsTo
    {
        return $this->belongsTo(I1Product::class, 'i1_base_product_id');
    }
} 