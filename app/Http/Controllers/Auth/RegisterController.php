<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Events\UserActivityLog;
use App\Constants\DbConstant as cn;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            cn::USERS_NAME_COL      => ['required', 'string', 'max:255'],
            cn::USERS_EMAIL_COL     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            cn::USERS_PASSWORD_COL  => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            cn::USERS_NAME_COL      => $data[cn::USERS_NAME_COL],
            cn::USERS_EMAIL_COL     => $data[cn::USERS_EMAIL_COL],
            cn::USERS_PASSWORD_COL  => Hash::make($data[cn::USERS_PASSWORD_COL]),
        ]);
    }
}
