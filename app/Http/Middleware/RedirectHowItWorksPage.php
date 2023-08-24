<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectHowItWorksPage
{
    /**
     * Redirect "how-it-works" url to wordpress "/home/how-it-works" url page
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentroute = \Request::getRequestUri();
        if($currentroute == '/how-it-works'){
            return redirect('/home/how-it-works');
        }
        return $next($request);
    }
}
