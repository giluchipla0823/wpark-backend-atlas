<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param $request
     * @return string|void|null
     * @throws AuthorizationException
     */
    protected function redirectTo($request)
    {
        if($request->is('api/*')) {
            throw new AuthorizationException('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
