<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Placeholder middleware — not using Inertia.
 * Kept in bootstrap/app.php pipeline for easy future swap.
 */
class HandleInertiaRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
