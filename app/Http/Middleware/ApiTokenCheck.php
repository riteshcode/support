<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helper\ApiHelper;

class ApiTokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->has('api_token')){

            if(ApiHelper::api_token_validate($request->api_token)){
                $request->api_token = ApiHelper::get_api_token($request->api_token);
                return $next($request)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');;
            }else
                return ApiHelper::JSON_RESPONSE(false,null,'Api token is invalid');

        }else{
            return ApiHelper::JSON_RESPONSE(false,null,'Api token is missing from your request !');
        }
    }
}
