<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(Auth::guard($guard)->check() && Auth::user()->role_id == 1){ // SUPERADMIN_ROLE_ID
            return redirect()->route('superadmin.dashboard');
            //return redirect()->route('users.index');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 2){ // TEACHER_ROLE_ID
            return redirect()->route('teacher.dashboard');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 3){ // STUDENT_ROLE_ID
            return redirect()->route('student.dashboard');
            //return redirect()->route('getStudentExamList');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 5){ // SCHOOL_ROLE_ID
            return redirect()->route('student.dashboard');
            //return redirect()->route('report.class-test-reports.correct-incorrect-answer');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 7){ // PRINCIPAL_ROLE_ID
            return redirect()->route('principal.dashboard');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 8){ // PANEL_HEAD_ROLE_ID
            return redirect()->route('panel-head.dashboard');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 9){ // CO_ORDINATOR_ROLE_ID
            return redirect()->route('co-ordinator.dashboard');
        }else{
            return $next($request);
        }
    }
}
