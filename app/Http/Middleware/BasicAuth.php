<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('basic-auth.users')->contains([$request->getUser(), $request->getPassword()])) {
            return $next($request);
        }
        throw new Exception('Access denied.', Response::HTTP_UNAUTHORIZED);
        // return response('Access denied', 401, ['WWW-Authenticate' => 'Basic']);
    }
}
