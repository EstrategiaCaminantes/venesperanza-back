<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIToken
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
        
        
        $token = $request['headers']['Authorization'];
        if($token == env('APP_KEY')){
      
        return $next($request);
        }
        
    }
}
