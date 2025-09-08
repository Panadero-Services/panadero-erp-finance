<?php

use Illuminate\Support\Facades\Route;

// Public routes that don't require authentication
Route::get('indigo3/inventory', function () {
    return response()->view('indigo3-landing', [
        'title' => 'Indigo3 Inventory Management - Next-Generation ERP'
    ]);
})->name('indigo3.inventory.landing');

Route::get('indigo3/compliance', function () {
    return response()->file(public_path('indigo3-compliance.html'));
})->name('indigo3.compliance.landing');
