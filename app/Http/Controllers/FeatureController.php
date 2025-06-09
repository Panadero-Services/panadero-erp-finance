<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::all();
        $features->each(function ($feature) {
            $feature->options = json_decode($feature->options, true);
        });
        return response()->json($features->toArray());
    }
} 