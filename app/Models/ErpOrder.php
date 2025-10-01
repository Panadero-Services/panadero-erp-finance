<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialIdentifier;

class ErpOrder extends Model
{
    use HasFactory, HasSequentialIdentifier;

    // Generate order identifier like "ORD00001"
    public static function generateOrderIdentifier()
    {
        return static::generateIdentifier('ORD', 5);
    }
}
