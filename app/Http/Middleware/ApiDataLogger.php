<?php
namespace App\Http\Middleware;
use Closure;
use App\Helpers\Helpers as Helper;
use Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class ApiDataLogger
{
    private $startTime;
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        $this->startTime = microtime(true);
        return $next($request);
    }
    public function terminate($request, $response)
    {
        // Log only for logged in user
        if(isset(Auth::user()->id) && !empty(Auth::user()->id)){

            if (env('API_DATALOGGER', false)) {
                return;
            }

            $end_time = microtime(true);
            $filename = 'api_datalogger_' . date('d-m-Y') . '.log';
            $main = array();
            $main['Time'] = gmdate("F j, Y, g:i a");
            //$main['Duration'] = number_format($end_time - LARAVEL_START, 3);
            $main['Duration'] = 2;
            $main['IP Address'] = $request->ip();
            $main['URL'] = $request->fullUrl();
            $main['Method'] = $request->method();
            $main['Input'] = $request->all();
            //Unset if password field exist
            if(isset($main['Input']->password) && $main['Input']->password != '')
                {
                    unset($main['Input']->password);
                }
            $main['Output'] = $response->getContent();
            $data = json_encode($main, JSON_PRETTY_PRINT);
            //  \File::append(public_path('apilogs' . DIRECTORY_SEPARATOR . $filename), $data. "\n" . str_repeat("=", 20) . "\n\n");
            
            // Store logs
            $url = $request->fullUrl();
            $requestData = json_encode($request->all());
            $responseData=$response->getContent();
            $type = 0;
            Helper::storeLogs($url,$requestData,$responseData,$type,'','');
        }
    }
}

?>
