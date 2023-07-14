<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class GameBundle extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = cn::GAME_SCHOOLS_BUNDLE_TABLE;

    protected $fillable = [
        cn::GAME_SCHOOLS_BUNDLE_SCHOOL_ID_COL,
        cn::GAME_SCHOOLS_BUNDLE_USER_ID_COL,
        cn::GAME_SCHOOLS_BUNDLE_IS_ADMIN_UPDATED,
        cn::GAME_SCHOOLS_BUNDLE_VALUES_COL
    ];

}
