<?php

namespace Glumbo\Gracart\Api\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiConnection
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
        if (!gc_config('api_connection_required')) {
            return $next($request);
        }
        $apiconnection = $request->header('apiconnection');
        $apikey = $request->header('apikey');
        if (!$apiconnection || !$apikey) {
            return  response()->json(['error' => 1, 'msg' => 'apiconnection or apikey not found']);
        }
        $check = \Glumbo\Gracart\Front\Models\ShopApiConnection::check($apiconnection, $apikey);
        if ($check) {
            $check->update(['last_active' => gc_time_now()]);
            return $next($request);
        } else {
            return  response()->json(['error' => 1, 'msg' => 'Connection not correct']);
        }
        return $next($request);
    }
}
