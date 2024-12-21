<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyFastApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('app.APP_FAST_API_KEY');

        $apiKeyIsValid = (
            filled($apiKey)
            && $request->header('x-api-key') === $apiKey
        );

        abort_if (! $apiKeyIsValid, 403, 'Access denied You BAstaRD!!');

        return $next($request);
    }
}
