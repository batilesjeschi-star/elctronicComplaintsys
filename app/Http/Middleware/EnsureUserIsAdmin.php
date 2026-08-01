<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Block access to anyone who isn't logged in as an admin.
     * Registered as the 'admin' alias in bootstrap/app.php.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
