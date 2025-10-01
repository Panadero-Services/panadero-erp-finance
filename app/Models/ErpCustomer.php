<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialIdentifier;

class ErpCustomer extends Model
{
    use HasFactory, HasSequentialIdentifier;

    protected $table = 'erp_customers';

    protected $fillable = [
        'name',
        'identifier',
        'json',
        'comment',
        'is_active',
        'is_locked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'json' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function ordersOut()
    {
        return $this->hasMany(ErpOrdersOut::class, 'customer_id');
    }

    // Generate customer identifier like "CUS00001"
    public static function generateCustomerIdentifier()
    {
        return static::generateIdentifier('CUS', 5);
    }
}

