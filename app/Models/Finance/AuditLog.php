<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'finance_audit_logs';
    protected $fillable = ['user_id','entity','action','meta','ip'];
    protected $casts = ['meta' => 'array'];
}