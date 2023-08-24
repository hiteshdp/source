<?php

namespace App\Http\Middleware;

use Closure;
use Response;

use App\Models\Settings;

class IpMiddleware
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
        $ips = Settings::where('name','allowed_ips')->select('value')->first()->toArray();

        if($ips['value'] != '')
        {
            $ips = explode(",",$ips['value']);
            
            if (!in_array($request->ip(),$ips)) {
                
                $this->msg['status'] = 0;
                $this->msg['message'] = "Not Allow to Access for IP (".$request->ip().")";

                return response()->json($this->msg,400);

            }
            return $next($request);
        }
        if($ips['value'] == '')
        {
            return $next($request);
        }   
    }
}
