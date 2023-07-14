<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;

class Regions extends Model
{
    use HasFactory;

    protected $table = cn::REGIONS_TABLE;

    public $fillable = [
        cn::REGIONS_REGION_EN_COL,
        cn::REGIONS_REGION_CH_COL,
        cn::REGIONS_STATUS_COL
    ];

    public $timestamps = true;
}
