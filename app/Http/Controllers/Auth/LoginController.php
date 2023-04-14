<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant as cn;
use Log;
use Session;
use App\Events\UserActivityLog;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    use Common;
    use ResponseFormat;

    //protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = $this->isLogged(); // User has already logged then redirect
        $this->middleware('guest')->except('logout');
    }

    /**
     * USE : Landing on login page
     */
    public function index(Request $request){
        $loginType = '';
        if($request->route()->getName() == 'super-admin.login'){
            $loginType = 'superadmin';
        }
        return view('auth.login',compact('loginType'));
    }

    /**
     * 
     */
    public function logincheck(Request $request){
        try{
            $credential = $request->only(cn::USERS_EMAIL_COL,cn::USERS_PASSWORD_COL);
            if(isset($request->login_type) && $request->login_type == 'superadmin'){ // This is only login as "super admin"
                $User = User::with('roles')->where([cn::USERS_EMAIL_COL => $credential['email']])->whereIn(cn::USERS_ROLE_ID_COL,[cn::SUPERADMIN_ROLE_ID])->first();
            }else{  // This is normal login as "school, Student, Parent"
                $User = User::with('roles')->where([cn::USERS_EMAIL_COL => $credential['email']])->whereNotIn(cn::USERS_ROLE_ID_COL,[cn::SUPERADMIN_ROLE_ID])->first();
            }
            if(!isset($User) && empty($User)){
                return response()->json(array('status' => 0,'message'=>'Please enter registered email'));
            }
            // If user is found the check credentials
            if(Hash::check($request->password, $User->{cn::USERS_PASSWORD_COL})) {
                Auth::login($User);
                // Set User Log Activities
                //$this->UserActivitiesLogs('login');
                $redirectUrl = $this->GetRedirectURL();
                $this->UserActivityLog(
                    Auth::user()->{cn::USERS_ID_COL},
                    '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.login_history_text').'</p>'
                );
                return $this->sendResponse([
                        'redirectUrl' => $redirectUrl,
                        'user_role' => Auth::user()->{cn::USERS_ROLE_ID_COL}
                    ],
                    'Login Successfully'
                );
            }else{
                return $this->sendError('Invalid Login Credentials', 422);
            }
        }catch(\Exception $ex){
            Log::info('Login Failed :'.json_encode($ex));
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function logout(Request $request) {
        try {
            $redirectUrl = config()->get('app.url').'login';
            $this->UserActivityLog(
                Auth::user()->{cn::USERS_ID_COL}, 
                '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.logout_history_text').'</p>'
            );
            // Set User Log Activities
            if(Auth::user()){
                //$this->UserActivitiesLogs('logout');
                if(Auth::user()->{cn::USERS_ROLE_ID_COL} == 1){
                    $redirectUrl = config()->get('app.url').'super-admin/login';
                }else{
                    $redirectUrl = config()->get('app.url').'login';
                }
            }
            Auth::logout();
            Session::flush();
            $request->session()->flush();
            Log::info('Last Logout : '.date('Y-m-d h:i:s'));
            return $this->sendResponse(['redirectUrl' => $redirectUrl], 'Logout Successfully');
        } catch (\Exception $ex) {
            Log::info('Login Failed :'.json_encode($ex));
            return $this->sendError($ex->getMessage(), 404);
        }
    }
}