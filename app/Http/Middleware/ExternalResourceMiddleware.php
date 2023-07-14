<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Constants\DbConstant as cn;
use Auth;

class ExternalResourceMiddleware
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
        if(auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == 6){
            return $next($request);
        }else {
            return redirect()->route('login');
        }
    }
}
