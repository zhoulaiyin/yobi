<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis as Redis;

class WapService
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
        global $WS;

        if(!$WS->isCustomerLogin){
            return redirect('/webi/wap/login?redirect_url=' . urlencode($request->url()));
        }

        return $next($request);
    }
}