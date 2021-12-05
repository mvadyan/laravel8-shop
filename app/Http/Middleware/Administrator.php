<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Administrator
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @param null $quard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $quard = null)
    {
        if (!auth()->user()->admin ) {
            abort(404);
        }

        return $next($request);
    }
}
