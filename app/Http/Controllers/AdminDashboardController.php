<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\Common;
use App\Events\UserActivityLog;

class AdminDashboardController extends Controller
{
    use Common;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }

    public function index(){
        return view('backend.admin_dashboard');
    }
}
