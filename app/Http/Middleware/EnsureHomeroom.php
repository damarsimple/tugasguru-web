<?php

namespace App\Http\Middleware;

use App\Enum\Ability;
use Closure;
use Illuminate\Http\Request;

class EnsureHomeroom
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
        $user = $request->user();

        if (
            !in_array(
                Ability::HOMEROOM,
                is_array($user->access)
                    ? $user->access
                    : json_decode($user->access)
            )
        ) {
            return response(["message" => "Unauthorized"], 401);
        }

        return $next($request);
    }
}
