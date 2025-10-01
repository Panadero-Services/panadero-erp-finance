<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpProductType extends Model
{
    use HasFactory;

    protected $table = 'erp_product_types';

    protected $fillable = [
        'name',
        'description'
    ];
}

