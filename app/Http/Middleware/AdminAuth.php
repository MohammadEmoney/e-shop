<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards admin routes: requires authentication on the dedicated admin guard
 * (username + password). Guests are redirected to the admin login page.
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->guest()) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
