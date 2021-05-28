<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureXendit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('x-callback-token') !== 'be4d203f6fd02e7f716d81947970991c389408305aba72e0b27f470b4967077b') {
            return response(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
