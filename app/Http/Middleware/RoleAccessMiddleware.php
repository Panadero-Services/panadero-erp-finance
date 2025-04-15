<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
//use App\Models\Role;

class RoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ... $roles): Response
    {
        if (!Auth::check()){
            return redirect()->route('login');
        }
      //  if (!Auth::user()->hasRole($role)){
      //      abort(403, 'JaWsome says: Unauthorized Action!!');
      //  }
        if (!Auth::user()->hasAnyRole($roles)){
            abort(403, 'JaWsome says: Unauthorized Action!!');
        }
        return $next($request);
    }
}
