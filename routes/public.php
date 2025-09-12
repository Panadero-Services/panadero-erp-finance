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

Route::get('indigo3/dashboard', function () {
    return response()->file(public_path('indigo3-dashboard.html'));
})->name('indigo3.dashboard.landing');

Route::get('indigo3/ai-phase11', function () {
    return response()->file(public_path('indigo3-ai-phase11.html'));
})->name('indigo3.ai.phase11.landing');

Route::get('indigo3/ai-phase12', function () {
    return response()->file(public_path('indigo3-ai-phase12.html'));
})->name('indigo3.ai.phase12.landing');

Route::get('indigo3/ai-phase11/workbench', function () {
    return response()->file(public_path('indigo3-ai-phase11-workbench.html'));
})->name('indigo3.ai.phase11.workbench');

Route::get('indigo3/ai-phase11/statistical-workbench', function () {
    return response()->file(public_path('indigo3-ai-phase11-statistical-workbench.html'));
})->name('indigo3.ai.phase11.statistical.workbench');

Route::get('indigo3/ai-phase11/timeseries-workbench', function () {
    return response()->file(public_path('indigo3-ai-phase11-timeseries-workbench.html'));
})->name('indigo3.ai.phase11.timeseries.workbench');

Route::get('indigo3/ai-phase11/matrix-workbench', function () {
    return response()->file(public_path('indigo3-ai-phase11-matrix-workbench.html'));
})->name('indigo3.ai.phase11.matrix.workbench');

Route::get('indigo3/ai-phase11/modeling-workbench', function () {
    return response()->file(public_path('indigo3-ai-phase11-modeling-workbench.html'));
})->name('indigo3.ai.phase11.modeling.workbench');
