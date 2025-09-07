<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceInvoiceController;

/*
|--------------------------------------------------------------------------
| Finance Invoice API Routes
|--------------------------------------------------------------------------
|
| These routes handle all finance invoice operations including:
| - CRUD operations for invoices
| - Invoice approval workflow
| - Statistics and reporting
| - CSV export functionality
|
*/

Route::prefix('finance/invoices')->middleware(['auth:sanctum'])->group(function () {
    
    // Basic CRUD operations
    Route::get('/', [FinanceInvoiceController::class, 'index']);
    Route::post('/', [FinanceInvoiceController::class, 'store']);
    Route::get('/{invoice}', [FinanceInvoiceController::class, 'show']);
    Route::put('/{invoice}', [FinanceInvoiceController::class, 'update']);
    Route::delete('/{invoice}', [FinanceInvoiceController::class, 'destroy']);
    
    // Invoice workflow operations
    Route::post('/{invoice}/approve', [FinanceInvoiceController::class, 'approve']);
    Route::post('/{invoice}/complete', [FinanceInvoiceController::class, 'complete']);
    Route::post('/{invoice}/cancel', [FinanceInvoiceController::class, 'cancel']);
    
    // Reporting and statistics
    Route::get('/stats/summary', [FinanceInvoiceController::class, 'stats']);
    Route::get('/export/csv', [FinanceInvoiceController::class, 'export']);
    
    // Section-specific routes
    Route::prefix('section/{section}')->group(function () {
        Route::get('/', [FinanceInvoiceController::class, 'index']);
        Route::get('/stats', [FinanceInvoiceController::class, 'stats']);
        Route::get('/export', [FinanceInvoiceController::class, 'export']);
    });
    
    // Status-specific routes
    Route::prefix('status/{status}')->group(function () {
        Route::get('/', [FinanceInvoiceController::class, 'index']);
        Route::get('/export', [FinanceInvoiceController::class, 'export']);
    });
});

/*
|--------------------------------------------------------------------------
| Finance Invoice Public Routes (if needed)
|--------------------------------------------------------------------------
|
| These routes can be used for public invoice viewing or webhook endpoints
|
*/

// Public invoice viewing (if needed)
Route::get('/finance/invoices/public/{invoice}', [FinanceInvoiceController::class, 'showPublic'])
    ->middleware(['throttle:60,1']); // Rate limiting for public access

// Webhook endpoints for external systems
Route::post('/finance/invoices/webhook', [FinanceInvoiceController::class, 'webhook'])
    ->middleware(['throttle:30,1']); // Rate limiting for webhooks

