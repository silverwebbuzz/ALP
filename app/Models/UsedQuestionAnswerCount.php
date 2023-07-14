<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;

class UsedQuestionAnswerCount extends Model
{
    use HasFactory;

    protected $table = cn::USED_QUESTION_ANSWER_COUNT_TABLE;
    
    public $fillable = [
        cn::USED_QUESTION_ANSWER_QUESTION_ID_COL,
        cn::USED_QUESTION_ANSWER_QUESTION_COUNT_COL,
        cn::USED_QUESTION_ANSWER_ANSWER1_COUNT_COL,
        cn::USED_QUESTION_ANSWER_ANSWER2_COUNT_COL,
        cn::USED_QUESTION_ANSWER_ANSWER3_COUNT_COL,
        cn::USED_QUESTION_ANSWER_ANSWER4_COUNT_COL,
        cn::USED_QUESTION_ANSWER_ANSWER5_COUNT_COL
    ];

    public $timestamps = false;
}
