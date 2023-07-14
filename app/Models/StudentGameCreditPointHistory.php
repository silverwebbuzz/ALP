<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\DbConstant as cn;
use App\Traits\Common;

class StudentGameCreditPointHistory extends Model
{
    use Common, SoftDeletes,HasFactory,Sortable;

    protected $table = cn::STUDENT_GAME_CREDIT_POINT_HISTORY_TABLE;

    public $fillable = [
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_GAME_ID_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_PLANET_ID_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_USER_ID_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_CURRENT_CREDIT_POINT_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_DEDUCT_CURRENT_STEP_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_DEDUCTED_STEPS_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_INCREASED_STEPS_COL,
        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_REMAINING_CREDIT_POINT_COL
    ];
}
