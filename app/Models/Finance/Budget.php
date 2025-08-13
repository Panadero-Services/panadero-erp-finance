<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'finance_budgets';
    protected $fillable = ['period'];

    public function lines() {
        return $this->hasMany(BudgetLine::class, 'budget_id');
    }
}