<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class WeatherDetail extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = cn::WEATHER_DETAIL_TABLE;

    public $fillable = [
        cn::WEATHER_DETAIL_WEATHER_INFO_COL
    ];

    public $timestamps = true;
}
