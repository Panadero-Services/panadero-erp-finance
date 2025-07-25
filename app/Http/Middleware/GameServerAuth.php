<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class GameServerAuth
{
    public function handle(Request $request, Closure $next)
    {
        $validToken = env('GAME_SERVER_TOKEN', 'local-dev-token');
        if ($request->header('X-Game-Server-Token') !== $validToken) {
            return response()->json(['error' => 'Invalid game server token'], 401);
        }
        return $next($request);
    }
}