<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;

class LearningObjectivesProgressReport extends Model
{
    use HasFactory;

    protected $table = cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_TABLE;

    public $fillable = [
        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,
        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_ALL_COL,
        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_TEST_COL,
        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_TESTING_ZONE_COL
    ];

    public $timestamps = true;
}
