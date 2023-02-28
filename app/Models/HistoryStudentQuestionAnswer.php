<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;

class HistoryStudentQuestionAnswer extends Model
{
    use HasFactory;

    protected $table = cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE;

    public $fillable = [
        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_NO_OF_SECOND_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL,
        cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL
    ];

    public $timestamps = false;
}
