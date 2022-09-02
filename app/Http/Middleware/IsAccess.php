<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class IsAccess
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
        $Auth=Auth::user();
        $method = $request->server('REQUEST_METHOD');
        $userPermissions=  $Auth->data()->role['permissions'];
        $path = Str::of($request->path())->split('%[/]+%')[1];
        $countUserPermissions=count($userPermissions);
        for ($i=0;$i<$countUserPermissions;$i++){
            if($userPermissions[$i]['feature'] == $path){
                $countUserPermissionsRoutes=count($userPermissions[$i]['routes']);
                 for($j=0;$j< $countUserPermissionsRoutes;$j++){
                     if(strtoupper($userPermissions[$i]['routes'][$j])==$method){
                         Log::info("User with email {$Auth->email} enter users successfully");
                        return $next($request);
                     }
                 }
            }
        }
        Log::error("User with email {$Auth->email} try enter users  but not successfully!");
        return response()->json(['error' =>"Forbidden access" ], 403);


    }
}


