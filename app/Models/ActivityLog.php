<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant As cn;
use Kyslik\ColumnSortable\Sortable;
use App\Models\User;
use App\Models\School;

class ActivityLog extends Model
{
    use HasFactory, Sortable;

    protected $table = cn::ACTIVITY_LOG_TABLE;

    public $fillable = [
        cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL,
        cn::ACTIVITY_LOG_SCHOOL_ID_COL,
        cn::ACTIVITY_LOG_USER_ID_COL,
        cn::ACTIVITY_LOG_ACTIVITY_LOG_COL,
    ];

    public $timestamps = true;

    // Enable sortable columns name
    public $sortable = [
        cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL,
        cn::ACTIVITY_LOG_SCHOOL_ID_COL,
        cn::ACTIVITY_LOG_USER_ID_COL
    ];

    public function user(){
        return $this->hasOne(User::class,cn::USERS_ID_COL, cn::ACTIVITY_LOG_USER_ID_COL);
    }

    public function school(){
        return $this->hasOne(School::class,cn::SCHOOL_ID_COLS, cn::ACTIVITY_LOG_SCHOOL_ID_COL);
    }
}
