<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    /*
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }*/
    
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
         $user = \App\Models\User::where('remember_token', $token)->first();
        if ($user) {
            //auth()->login($user);
            return $next($request);
            
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    }
    
}
