<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant as cn;

class PrincipalMiddleware
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
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == 7){
            return $next($request);
        }else {
            return redirect()->route('login');
        }
    }
}
