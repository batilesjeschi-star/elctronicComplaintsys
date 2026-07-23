<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks access to /admin/* routes unless the logged-in user has is_admin = true.
 * Registered as the 'admin' middleware alias in bootstrap/app.php.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_admin) {
            abort(403, 'Unauthorized. Admins only.');
        }

        return $next($request);
    }
}
