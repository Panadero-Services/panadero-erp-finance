<?php

use Illuminate\Support\Facades\Route;

// Public routes that don't require authentication

// Note: ERP configuration routes moved to routes/api.php to avoid conflicts

// Alternative config route with different path
Route::get('config/erp_products', function () {
    return response()->json(\App\Models\ErpProduct::getEnhancedDataTableConfig());
})->name('config.erp.products');

// Test route to verify public routes work
Route::get('api/test-public', function () {
    return response()->json(['message' => 'Public route working', 'timestamp' => now()]);
});

// Test route without api prefix
Route::get('test-public', function () {
    return response()->json(['message' => 'Public route without api prefix working', 'timestamp' => now()]);
});
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

// Test route outside API prefix
Route::get('/test-brands-direct', function () {
    return response()->json(\App\Models\ErpBrand::all());
});

// Direct ERP data routes that work without authentication
Route::get('/customers', function () {
    return response()->json(\App\Models\ErpCustomer::all());
});

Route::get('/suppliers', function () {
    return response()->json(\App\Models\ErpSupplier::all());
});

Route::get('/products', [\App\Http\Controllers\ErpDataController::class, 'products']);

// Model configuration for DataTable
Route::get('/model-config/{table}', function ($table) {
    $modelClass = 'App\\Models\\' . \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($table));
    
    if (!class_exists($modelClass)) {
        return response()->json([
            'success' => false,
            'message' => "Model {$modelClass} not found"
        ], 404);
    }
    
    $modelInstance = new $modelClass();
    
    $config = [
        'relationships' => method_exists($modelInstance, 'getDataTableRelationships') 
            ? $modelInstance->getDataTableRelationships() 
            : [],
        'formFields' => method_exists($modelInstance, 'formFields') 
            ? $modelInstance->formFields() 
            : [],
        'searchableColumns' => method_exists($modelInstance, 'getSearchableColumns') 
            ? $modelInstance->getSearchableColumns() 
            : [],
        'dataTableConfig' => method_exists($modelInstance, 'getDataTableConfig') 
            ? $modelInstance->getDataTableConfig() 
            : []
    ];
    
    return response()->json($config);
});

Route::get('/orders-in', function () {
    return response()->json(\App\Models\ErpOrdersIn::all());
});

Route::get('/orders-out', function () {
    return response()->json(\App\Models\ErpOrdersOut::all());
});

Route::get('/order-in-lines', function () {
    return response()->json(\App\Models\ErpOrderInLine::all());
});

Route::get('/order-out-lines', function () {
    return response()->json(\App\Models\ErpOrderOutLine::all());
});

Route::get('/sites', function () {
    return response()->json(\App\Models\ErpSite::all());
});

Route::get('/storages', function () {
    return response()->json(\App\Models\ErpStorage::all());
});

Route::get('/stocks', function () {
    return response()->json(\App\Models\ErpStock::all());
});

Route::get('/status', function () {
    return response()->json(\App\Models\ErpStatus::all());
});

Route::get('/units', function () {
    return response()->json(\App\Models\ErpUnit::all());
});

Route::get('/product-types', function () {
    return response()->json(\App\Models\ErpProductType::all());
});

Route::get('/event-handlers', function () {
    return response()->json(\App\Models\ErpEventHandler::all());
});

Route::get('/stock-handlers', function () {
    return response()->json(\App\Models\ErpStockHandler::all());
});

Route::get('/samples', function () {
    return response()->json(\App\Models\ErpSample::all());
});

Route::get('/analyses', function () {
    return response()->json(\App\Models\ErpAnalysis::all());
});

Route::get('/analyse-properties', function () {
    return response()->json(\App\Models\ErpAnalyseProperty::all());
});

Route::get('/analyse-lines', function () {
    return response()->json(\App\Models\ErpAnalyseLine::all());
});

Route::get('/product-groups', function () {
    return response()->json(\App\Models\ErpProductGroup::all());
});

// ============================================================================
// PUBLIC ERP API ROUTES (No Authentication Required)
// ============================================================================
Route::prefix('api')->group(function () {
    // Test route first
   // Route::get('/test-erp-public', function () {
   //     return response()->json(['message' => 'Public ERP API is working', 'data' => \App\Models\ErpCustomer::take(3)->get()]);
   // });
    
    // Test brands route directly
   // Route::get('/test-brands', function () {
   //     return response()->json(\App\Models\ErpBrand::all());
   // })->withoutMiddleware(['web', 'auth']);
    
    //Route::get('/erp_customers', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.customers');
    //Route::get('/erp_suppliers', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.suppliers');
    Route::get('/erp_products', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.products');
    Route::get('/erp_storages', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.storages');
    //Route::delete('/erp_products/{id}', [\App\Http\Controllers\DynamicController::class, 'destroyPublic'])->name('api.erp.products.destroy');
    //Route::get('/erp_orders_in', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.orders-in');
    //Route::get('/erp_orders_out', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.orders-out');
    //Route::get('/erp_order_in_lines', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.order-in-lines');
    //Route::get('/erp_order_out_lines', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.order-out-lines');
    Route::get('/erp_sites', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.sites');
    Route::get('/erp_storages', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.storages');
    //Route::get('/erp_stocks', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.stocks');
    //Route::get('/erp_status', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.status');
    Route::get('/erp_units', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.units');
    //Route::get('/erp_product_types', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.product-types');
    //Route::get('/erp_product_groups', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.product-groups');
    //Route::get('/erp_brands', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.brands');
    //Route::get('/erp_event_handlers', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.event-handlers');
    //Route::get('/erp_stock_handlers', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.stock-handlers');
    //Route::get('/erp_samples', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.samples');
    //Route::get('/erp_analyses', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.analyses');
    //Route::get('/erp_analyse_properties', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.analyse-properties');
    //Route::get('/erp_analyse_lines', [\App\Http\Controllers\DynamicController::class, 'api'])->name('api.erp.analyse-lines');
});
