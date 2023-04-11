<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class HistoryStudentExams extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = cn::HISTORY_STUDENT_EXAMS_TABLE;

    public $fillable = [
        cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL,
        cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL,
        cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL,
        cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL,
        cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL,
        cn::HISTORY_STUDENT_EXAMS_BEFORE_EMOJI_ID_COL,
        cn::HISTORY_STUDENT_EXAMS_AFTER_EMOJI_ID_COL,
        cn::HISTORY_STUDENT_EXAMS_TOTAL_SECONDS_COL,
        cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL,
        cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL,
        cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL,
        cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL
    ];

    public $timestamps = true;
}
