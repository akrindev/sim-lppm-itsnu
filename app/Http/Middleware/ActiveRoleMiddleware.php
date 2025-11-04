<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveRoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $activeRole = session('active_role');

            // If no active role set, use the first role
            if (! $activeRole || ! $user->hasRole($activeRole)) {
                $firstRole = $user->getRoleNames()->first();

                if ($firstRole) {
                    session(['active_role' => $firstRole]);
                }
            }
        }

        return $next($request);
    }
}
