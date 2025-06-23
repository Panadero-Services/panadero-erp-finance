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
            // Only decode if it's a string (backward compatibility)
            if (is_string($service->options)) {
                $service->options = json_decode($service->options, true) ?? [];
            }
        });
        return response()->json($services->toArray());
    }
} 