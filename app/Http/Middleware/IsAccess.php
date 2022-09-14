<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
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
        $path = Str::of($request->path())->split('%[/]+%');
        $countUserPermissions=count($userPermissions);//5
        for ($i=0;$i<$countUserPermissions;$i++){
            if($userPermissions[$i]['feature'] == $path[1]){
                $countUserPermissionsRoutes=count($userPermissions[$i]['routes']);
                 for($j=0;$j< $countUserPermissionsRoutes;$j++){
                     if(strtoupper($userPermissions[$i]['routes'][$j])==$method){
                         Log::info("User with email {$Auth->email} enter {$path[1]} successfully");
                        return $next($request);
                     }
                 }
            }
        }
        Log::error("User with email {$Auth->email} try enter {$path[1]}  but not successfully!");
        return response()->json(['error' =>"Forbidden access" ], 403);


    }
}


