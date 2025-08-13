<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    protected $table = 'finance_budget_lines';
    protected $fillable = ['budget_id','account','amount'];
}