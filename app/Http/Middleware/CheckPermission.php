<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check())
        {
            return response()->json([
                'message' => 'Unauthorized Please Log in First!'
            ], 403);
        }

        if (!Gate::allows($permission))
        {
            return response()->json([
                'message' => 'Forbidden. You Dont Have Permission To Access This Resource! '
            ], 403);
        }

        return $next($request);
    }
}
