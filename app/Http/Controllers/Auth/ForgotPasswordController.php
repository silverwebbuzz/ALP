<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request; 
use Mail; 
use Hash;
use Illuminate\Support\Str;
use App\Traits\ResponseFormat;
use DB; 
use Carbon\Carbon; 
use App\Models\User;
use App\Events\UserActivityLog;
use App\Constants\DbConstant as cn;

class ForgotPasswordController extends Controller
{
    use ResponseFormat;
    
    public function showForgetPasswordForm(){
        return view('auth.forgetPassword');
    }

    public function submitForgetPasswordForm(Request $request){
        if($request->email){
            if(User::where(cn::USERS_EMAIL_COL,$request->email)->doesntExist()){
                return $this->sendError('Please enter registered email', 422);
            }
            $token = Str::random(64);
            DB::table(cn::PASSWORD_RESETS_TABLE_NAME)->insert([
                cn::PASSWORD_RESETS_EMAIL_COL => $request->email, 
                cn::PASSWORD_RESETS_TOKEN_COL => $token, 
                cn::PASSWORD_RESETS_CREATED_AT_COL => Carbon::now()
            ]);
            $dataSet = [
                cn::PASSWORD_RESETS_TOKEN_COL => $token
            ];
            $sendEmail = $this->sendMails('email.forgetPassword', $dataSet, $request->email, $subject='Reset Password', [], []);
            return $this->sendResponse([], 'We have sent e-mailed your password reset link');
        }
    }

    public function showResetPasswordForm($token) {
        if(isset($token) && !empty($token)){
            if(DB::table(cn::PASSWORD_RESETS_TABLE_NAME)->where(cn::PASSWORD_RESETS_TOKEN_COL,$token)->doesntExist()){
                return redirect('forget-password')->withInput()->with('error_msg', 'Invalid token!');
            }else{
                return view('auth.forgetPasswordLink', [cn::PASSWORD_RESETS_TOKEN_COL => $token]);
            }
        }
    }

    public function submitResetPasswordForm(Request $request){
        if(DB::table(cn::PASSWORD_RESETS_TABLE_NAME)->where(cn::PASSWORD_RESETS_TOKEN_COL,$request->token)->doesntExist()){
            return $this->sendError('Invalid Token', 422);
        }else{
            $updatePassword = DB::table(cn::PASSWORD_RESETS_TABLE_NAME)->where(cn::PASSWORD_RESETS_TOKEN_COL,$request->token)->first();
            $user = User::where(cn::USERS_EMAIL_COL, $updatePassword->{cn::PASSWORD_RESETS_EMAIL_COL})->update(['password' => Hash::make($request->password)]);
            DB::table(cn::PASSWORD_RESETS_TABLE_NAME)->where([cn::USERS_EMAIL_COL => $updatePassword->{cn::PASSWORD_RESETS_EMAIL_COL}])->delete();
            $redirectUrl = config()->get('app.url').'login';
            return $this->sendResponse(['redirectUrl' => $redirectUrl], 'Your password has been changed successfully.');
        }
    }
}
