<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class AssetDisposal extends Model
{
    protected $table = 'finance_asset_disposals';
    protected $fillable = ['fixed_asset_id','disposed_at','proceeds','note'];
}