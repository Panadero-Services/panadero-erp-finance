<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed Asset
        DB::table('finance_fixed_assets')->updateOrInsert(
            ['name'=>'Lathe Machine'],
            [
                'category'=>'Equipment',
                'cost'=>12000,
                'acquired_at'=>now()->subMonths(2)->toDateString(),
                'method'=>'straight_line',
                'useful_life_months'=>36,
                'accumulated_depreciation'=>0,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]
        );

        // Budget example
        $period = now()->format('Y-m');
        $budgetId = DB::table('finance_budgets')->updateOrInsert(['period'=>$period], ['period'=>$period,'created_at'=>now(),'updated_at'=>now()]);
        $bid = DB::table('finance_budgets')->where('period',$period)->value('id');
        DB::table('finance_budget_lines')->updateOrInsert(['budget_id'=>$bid,'account'=>'Revenue'], ['amount'=>20000,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('finance_budget_lines')->updateOrInsert(['budget_id'=>$bid,'account'=>'Expenses'], ['amount'=>12000,'created_at'=>now(),'updated_at'=>now()]);
    }
}