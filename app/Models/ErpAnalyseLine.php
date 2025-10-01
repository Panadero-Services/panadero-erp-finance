<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpAnalyseLine extends Model
{
    use HasFactory;

    protected $table = 'erp_analyse_lines';

    protected $fillable = [
        'analysis_id',
        'property_id',
        'value',
        'comment'
    ];

    public function analysis()
    {
        return $this->belongsTo(ErpAnalysis::class, 'analysis_id');
    }

    public function property()
    {
        return $this->belongsTo(ErpAnalyseProperty::class, 'property_id');
    }
}

