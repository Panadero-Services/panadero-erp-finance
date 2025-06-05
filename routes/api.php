<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Http\Controllers\SomethingController;
use App\Http\Middleware\VerifyFastApiKey;
use App\Http\Middleware\EnsureTokenIsValid;

use App\Http\Controllers\BotController;
use App\Http\Controllers\ProviderController;

Route::apiResource('providers', ProviderController::class);

// Assuming you have a ProviderController
Route::get('/providers/{provider}/balance', [ProviderController::class, 'getContractBalance'])->name('providers.balance');

// ... other routes ...
Route::post('/providers/{provider}/test', [ProviderController::class, 'test'])->name('providers.test');

Route::prefix('bots')->group(function () {
    Route::get('/', [BotController::class, 'index']);
    Route::post('/', [BotController::class, 'store']);
    Route::put('/{id}', [BotController::class, 'update']);
    Route::delete('/{id}', [BotController::class, 'destroy']);
    Route::post('/{id}/start', [BotController::class, 'start']);
    Route::post('/{id}/stop', [BotController::class, 'stop']);
    Route::get('/{id}/status', [BotController::class, 'status']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/adm', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('test_user', function (){
    return Post::take(5)->get();
});

Route::get('whatever', function (){
    return Post::take(51)->get();
});



Route::get('u', function (Request $request){
    $response = $request;
    return $response;
})->middleware('auth:sanctum');


Route::post('/example', [SomethingController::class, 'justAnExample'])
->middleware(VerifyFastApiKey::class);

// Model configuration
Route::get('/model-config/{module}/{table}', [ModelConfigController::class, 'getModelConfig'])
    ->middleware(['auth:sanctum', 'verified']);

/*
Route::get('/examplez', [SomethingController::class, 'justAnExample']);

Route::group([
    'prefix' => 'v1',
    'middleware' => 'with_fast_api_key'
], function () {
    Route::post('/examplez', [SomethingController::class, 'justAnExample']);
    // ...
});
*/