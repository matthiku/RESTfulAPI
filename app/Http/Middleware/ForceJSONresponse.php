<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\HeaderBag;

class ForceJSONresponse
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // just for API calls, we want to never have anything but JSON responses!
        if( !str_contains($request->header('accept'), 'application/json') ) 
        {
            $request->server->set('HTTP_ACCEPT', 'application/json');
            $request->headers = new HeaderBag($request->server->getHeaders());
        }

        return $next($request);
    }
}
