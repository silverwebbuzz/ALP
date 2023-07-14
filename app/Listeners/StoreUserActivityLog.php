<?php

namespace App\Listeners;

use App\Events\UserActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant as cn;

class StoreUserActivityLog{
    
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserActivityLog  $event
     * @return void
     */
    public function handle(UserActivityLog $event)
    {
        $HistoryActivityData = $event->ActivityHistory;
        $UserData = User::find($HistoryActivityData['user_id']);
        $saveHistory =  ActivityLog::Create([
                            cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL    => $UserData->curriculum_year_id,
                            cn::ACTIVITY_LOG_SCHOOL_ID_COL             => $UserData->school_id,
                            cn::ACTIVITY_LOG_USER_ID_COL               => $UserData->id,
                            cn::ACTIVITY_LOG_ACTIVITY_LOG_COL          => $HistoryActivityData['ActivityMessage']
                        ]);
        return $saveHistory;
    }
}
