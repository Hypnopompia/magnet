<?php

namespace App\Http\Middleware;

use Closure;

class EB_SSL_Trust
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
        // ELBs sent requests to port 80 on the EC2 instance, so we need to force SSL
        $request->setTrustedProxies( [ $request->getClientIp() ] );
        return $next($request);
    }
}
