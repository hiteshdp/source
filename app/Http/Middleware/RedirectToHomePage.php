<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectToHomePage
{
    /**
     * Redirect root url to wordpress "/home" url page 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentroute = \Request::getRequestUri();
        if($currentroute == '/'){
            // 301 permanent redirection
            // return redirect('https://wellkasa.com',301);
        }
        return $next($request);
    }
}
