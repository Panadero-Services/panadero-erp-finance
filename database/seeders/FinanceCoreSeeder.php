<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceCoreSeeder extends Seeder
{
    public function run(): void
    {
        // Chart of Accounts (code is unique, and type is required)
        $accounts = [
            ['code'=>'1000','name'=>'Cash','type'=>'asset','subtype'=>null],
            ['code'=>'1100','name'=>'Accounts Receivable','type'=>'asset','subtype'=>null],
            ['code'=>'1200','name'=>'Inventory','type'=>'asset','subtype'=>null],
            ['code'=>'1600','name'=>'Fixed Assets','type'=>'asset','subtype'=>null],
            ['code'=>'1700','name'=>'Accumulated Depreciation','type'=>'asset','subtype'=>'contra'],
            ['code'=>'2000','name'=>'Accounts Payable','type'=>'liability','subtype'=>null],
            ['code'=>'3000','name'=>'Capital','type'=>'equity','subtype'=>null],
            ['code'=>'4000','name'=>'Revenue','type'=>'revenue','subtype'=>null],
            ['code'=>'5000','name'=>'Expenses','type'=>'expense','subtype'=>null],
            ['code'=>'5100','name'=>'Depreciation Expense','type'=>'expense','subtype'=>null],
        ];
        foreach ($accounts as $acc) {
            DB::table('finance_accounts')->updateOrInsert(
                ['code' => $acc['code']], // unique key
                [
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'subtype' => $acc['subtype'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }

        // Periods (this and next month) require start_date, end_date, status
        $now = Carbon::now();
        $periods = [
            $now->copy()->startOfMonth(),
            $now->copy()->addMonth()->startOfMonth(),
        ];
        foreach ($periods as $start) {
            $periodStr = $start->format('Y-m');
            $end = $start->copy()->endOfMonth();
            DB::table('finance_periods')->updateOrInsert(
                ['period' => $periodStr],
                [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'status' => 'open',
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }

        // Cashflow categories require 'type'
        $cats = [
            ['name'=>'Sales Revenue','type'=>'operating'],
            ['name'=>'Operating Expenses','type'=>'operating'],
        ];
        foreach ($cats as $cat) {
            DB::table('finance_cashflow_categories')->updateOrInsert(
                ['name' => $cat['name']],
                ['type' => $cat['type'], 'updated_at'=>now(), 'created_at'=>DB::raw('COALESCE(created_at, NOW())')]
            );
        }
    }
}