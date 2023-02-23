<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningObjectivesSkills extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = cn::LEARNING_OBJECTIVES_SKILLS_TABLE;

    public $fillable = [
        cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL,
        cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL
    ];

    public $timestamps = true;
}
