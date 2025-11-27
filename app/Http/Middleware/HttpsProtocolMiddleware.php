<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class HttpsProtocolMiddleware
{

    public function handle($request, Closure $next)
    {
            if (!$request->secure() && config('app.enforceHttps')) {
                return redirect()->secure($request->getRequestUri());
            }

            return $next($request); 
    }
}