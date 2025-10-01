<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpUnit extends Model
{
    use HasFactory;

    protected $table = 'erp_units';

    protected $fillable = [
        'name',
        'symbol',
        'is_decimal'
    ];

    protected $casts = [
        'is_decimal' => 'boolean'
    ];

    public function scopeDecimal($query)
    {
        return $query->where('is_decimal', true);
    }

    public function scopeInteger($query)
    {
        return $query->where('is_decimal', false);
    }
}

