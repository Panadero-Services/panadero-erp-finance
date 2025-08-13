<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    protected $table = 'finance_fixed_assets';
    protected $fillable = [
        'name','category','cost','acquired_at','method','useful_life_months','accumulated_depreciation'
    ];

    public function depreciations() {
        return $this->hasMany(AssetDepreciation::class, 'fixed_asset_id');
    }

    public function disposals() {
        return $this->hasMany(AssetDisposal::class, 'fixed_asset_id');
    }
}