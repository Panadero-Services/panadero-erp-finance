<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\FixedAsset;
use App\Models\Finance\AssetDepreciation;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function index() {
        return FixedAsset::with('depreciations')->orderBy('id','desc')->get();
    }

    public function store(Request $req) {
        $asset = FixedAsset::create($req->validate([
            'name'=>'required|string',
            'category'=>'nullable|string',
            'cost'=>'required|numeric',
            'acquired_at'=>'nullable|date',
            'method'=>'required|string',
            'useful_life_months'=>'required|integer|min:1',
        ]));
        return response()->json($asset, 201);
    }

    public function depreciate(Request $req) {
        $period = $req->string('period') ?: now()->format('Y-m');
        $assets = FixedAsset::all();
        $count = 0;
        foreach ($assets as $a) {
            if ($a->method !== 'straight_line' || !$a->useful_life_months) continue;
            $monthly = $a->cost / $a->useful_life_months;
            if ($monthly <= 0) continue;

            // record depreciation row
            AssetDepreciation::updateOrCreate(
                ['fixed_asset_id'=>$a->id, 'period'=>$period],
                ['amount'=>$monthly]
            );

            // update accumulated
            $a->increment('accumulated_depreciation', $monthly);
            $count++;
        }
        return ['ok'=>true,'period'=>$period,'entries'=>$count];
    }
}