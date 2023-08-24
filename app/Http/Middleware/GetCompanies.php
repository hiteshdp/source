<?php

namespace App\Http\Middleware;

use Closure;
use Cache;

class GetCompanies
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
        if (Cache::has('companies') == false) {
            $token = token_api();
            $companies = company_list($token);
            sort($companies);
            Cache::put('companies', $companies, 20);
        }

        return $next($request);
    }
}
