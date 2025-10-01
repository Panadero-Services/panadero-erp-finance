<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpAnalyseProperty extends Model
{
    use HasFactory;

    protected $table = 'erp_analyse_properties';

    protected $fillable = [
        'name',
        'unit',
        'data_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function analyseLines()
    {
        return $this->hasMany(ErpAnalyseLine::class, 'property_id');
    }
}

