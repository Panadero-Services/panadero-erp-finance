<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add Master Server routes to CSRF exclusion
        'master/*',
        'master/worlds/*',
        'master/player/*',
        'master/events/*',
        'master/update-score',
        'master/leaderboard',
        'master/player-stats',
        'master/health',
        'master/worlds/heartbeat'  // Add specific route
    ];
}