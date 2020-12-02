<?php

namespace App\Http\Middleware;
use Closure;

use Illuminate\Http\Request;



class DashboardAuthenticate 
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
    
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
     
         $user = \App\Models\User::where('remember_token', $token)->first();

        if ($user['email'] && $user['password']) {

            return $next($request);
            
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    }
    
}
