<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class I1BusinessCustomer extends Model
{
    use HasFactory;

    protected $table = 'i1_business_customers';

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
        'default_qty',
        'i1_forwarder_id',
        'i1_product_id',
        'i1_source_site_id',
        'docs_array',
        'analyse_array',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'default_qty' => 'decimal:2',
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

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(I1Forwarder::class, 'i1_forwarder_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(I1Product::class, 'i1_product_id');
    }

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(I1Site::class, 'i1_source_site_id');
    }
} 