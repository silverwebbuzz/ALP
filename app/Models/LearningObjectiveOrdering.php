<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\LearningsObjectives;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class LearningObjectiveOrdering extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = cn::LEARNING_OBJECTIVES_ORDERING_TABLE;

    protected $fillable = [
        cn::LEARNING_OBJECTIVES_ORDERING_ID_COL,
        cn::LEARNING_OBJECTIVES_ORDERING_SCHOOL_ID_COL,
        cn::LEARNING_OBJECTIVES_LEARNING_UNIT_ID_COL,
        cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_OBJECTIVE_ID_COL,
        cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_POSITION_COL,
        cn::LEARNING_UNIT_ORDERING_LEARNING_INDEX_COL
    ];

    public function learning_objective(){
        return $this->belongsTo(LearningsObjectives::Class,'learning_objective_id','id');
    }
    
}
