<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use  App\Models\LearningsUnits;

use App\Constants\DbConstant as cn;

class LearningUnitOrdering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        cn::LEARNING_UNIT_ORDERING_ID_COL,
        cn::LEARNING_UNIT_ORDERING_SCHOOL_ID_COL,
        cn::LEARNING_UNIT_STRAND_ID_COL,
        cn::LEARNING_UNIT_ORDERING_LEARNING_UNIT_ID_COL,
        cn::LEARNING_UNIT_ORDERING_LEARNING_POSITION_COL,
        cn::LEARNING_UNIT_ORDERING_LEARNING_INDEX_COL
    ];

    protected $table = cn::LEARNING_UNIT_ORDERING_TABLE;

    public function learning_unit(){
        return $this->belongsTo(LearningsUnits::Class,'learning_unit_id','id');
    }

}
