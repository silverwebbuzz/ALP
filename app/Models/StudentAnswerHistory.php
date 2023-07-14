<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant As cn;

class StudentAnswerHistory extends Model
{
    use HasFactory;

    protected $table = cn::STUDENT_ATTEMPT_EXAM_HISTORY_TABLE;
    
    public $fillable = [
        cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL,
        cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL,
        cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL,
        cn::STUDENT_ATTEMPT_EXAM_HISTORY_SELECTED_ANSWER_COL,
        cn::STUDENT_ATTEMPT_EXAM_HISTORY_LANGUAGE_COL
    ];

}
