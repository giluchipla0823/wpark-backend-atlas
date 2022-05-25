<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('basic-auth.users')->contains([$request->getUser(), $request->getPassword()])) {
            return $next($request);
        }
        throw new Exception('Access denied.', Response::HTTP_UNAUTHORIZED);
    }
}
