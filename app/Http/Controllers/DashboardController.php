<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Events\UserActivityLog;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }

    /**
     * USE : Panel Head Dashboard
     */
    public function PanelHead(){
        return view('backend.dashboard.panel_head');
    }

    /**
     * USE : CoOrdinator dashboard
     */
    public function CoOrdinator (){
        return view('backend.dashboard.co_ordinator');
    }
}
