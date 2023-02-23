<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\Common;
use App\Models\User;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
class AuthenticateUser
{
    use Common,ResponseFormat;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        $userId = $this->decrypt($request->student_id);
        if(User::where([cn::USERS_ID_COL => $userId,cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID])->exists()){
            return $next($request);
        }
        return $this->sendError(__('User Not Found'), 422);
    }
}
