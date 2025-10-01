<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpEventHandler extends Model
{
    use HasFactory;

    protected $table = 'erp_event_handlers';

    protected $fillable = [
        'module',
        'action',
        'start_plan',
        'end_plan',
        'start_actual',
        'end_actual',
        'due',
        'json',
        'comment'
    ];

    protected $casts = [
        'start_plan' => 'datetime',
        'end_plan' => 'datetime',
        'start_actual' => 'datetime',
        'end_actual' => 'datetime',
        'due' => 'datetime',
        'json' => 'array'
    ];

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due', '<', now())->whereNull('end_actual');
    }
}

