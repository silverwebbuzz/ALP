<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use App\Events\UserActivityLog;
use App\Constants\DbConstant as cn;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;
    
    //protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo;

    public function __construct()
    {
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == 1){
            $this->redirectTo = route('admin.dashboard');
        } elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == 2){
            $this->redirectTo = route('user.dashboard');
        }
       
        $this->middleware('guest')->except('logout');
    }
}
