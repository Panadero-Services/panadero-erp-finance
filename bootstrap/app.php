<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\RoleAccessMiddleware;
use App\Http\Middleware\LoadUserRoles;
use App\Http\Middleware\Cors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            Cors::class,
            ValidateCsrfToken::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            LoadUserRoles::class,
        ]);
        //
    })
//    ->withMiddleware(function (Middleware $middleware) {$middleware->append(EnsureTokenIsValid::class);})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
