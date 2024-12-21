<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Http\Controllers\SomethingController;
use App\Http\Middleware\VerifyFastApiKey;
use App\Http\Middleware\EnsureTokenIsValid;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/adm', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('test_user', function (){
    return Post::take(5)->get();
});

Route::get('u', function (Request $request){
    $response = $request;
    return $response;
})->middleware('auth:sanctum');


Route::post('/example', [SomethingController::class, 'justAnExample'])
->middleware(VerifyFastApiKey::class);

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