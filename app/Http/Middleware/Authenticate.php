<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /* public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $user = \App\User::where('api_token', $token)->first();
        if ($user) {
            auth()->login($user);
            return $next($request);
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    } */
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
