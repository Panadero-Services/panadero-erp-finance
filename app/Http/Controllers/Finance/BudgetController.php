<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Budget;
use App\Models\Finance\BudgetLine;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function show($period) {
        $budget = Budget::with('lines')->where('period',$period)->first();
        return $budget ?: ['period'=>$period,'lines'=>[]];
    }

    public function upsert(Request $req) {
        $data = $req->validate([
            'period'=>'required|string',
            'lines'=>'array',
            'lines.*.account'=>'required|string',
            'lines.*.amount'=>'numeric'
        ]);
        $budget = Budget::updateOrCreate(['period'=>$data['period']], ['period'=>$data['period']]);
        $budget->lines()->delete();
        foreach ($data['lines'] ?? [] as $line) {
            BudgetLine::create(['budget_id'=>$budget->id] + $line);
        }
        return Budget::with('lines')->find($budget->id);
    }
}