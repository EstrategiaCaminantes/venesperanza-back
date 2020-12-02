<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardApiToken
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
        
        $token = $request->bearerToken();


        $valueToken = env('APP_API_KEY');
      
        if($token  === $valueToken){
        return $next($request);
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);


    }
}
