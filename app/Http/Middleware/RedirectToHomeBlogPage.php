<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToHomeBlogPage
{
    /**
     * Redirect "blogs" url to wordpress "/home/blog" url page 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentroute = \Request::getRequestUri();
        if($currentroute == '/blogs'){
            return redirect('/home/blog');
        }
        return $next($request);
    }
}
