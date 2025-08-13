<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index() {
        return AuditLog::orderBy('id','desc')->limit(500)->get();
    }

    public function store(Request $req) {
        $payload = $req->validate([
            'entity'=>'required|string',
            'action'=>'required|string',
            'meta'=>'array'
        ]);
        $payload['user_id'] = optional($req->user())->id;
        $payload['ip'] = $req->ip();
        return AuditLog::create($payload);
    }
}