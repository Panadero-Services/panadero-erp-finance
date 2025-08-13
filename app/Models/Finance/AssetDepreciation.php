<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    protected $table = 'finance_asset_depreciations';
    protected $fillable = ['fixed_asset_id','period','amount'];
}