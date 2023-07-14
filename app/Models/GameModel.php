<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class GameModel extends Model{
    use HasFactory;

    protected $table = cn::GAME_TABLE;

    public $fillable = [
        cn::GAME_NAME_COL,
        cn::GAME_STATUS_COL
     ];
 
     public $timestamps = true;
}
