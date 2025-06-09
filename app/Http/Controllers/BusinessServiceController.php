<?php

namespace App\Http\Controllers;

use App\Models\BusinessService;
use Illuminate\Http\Request;

class BusinessServiceController extends Controller
{
    public function index()
    {
        $services = BusinessService::all();
        $services->each(function ($service) {
            $service->options = json_decode($service->options, true);
        });
        return response()->json($services->toArray());
    }
} 