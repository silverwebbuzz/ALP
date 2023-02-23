<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class GamePlanets extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = cn::GAME_PLANETS_TABLE;

    protected $fillable = [
        cn::GAME_PLANETS_ID_COL,
        cn::GAME_PLANETS_NAME_COL,
        cn::GAME_PLANETS_IMAGE_COL,
        cn::GAME_PLANETS_STATUS_COL
    ];

    protected $appends = [
        'PlanetImageUrl'
    ];

    public function getPlanetImageUrlAttribute(){
        $planet_image_url = '';
        if(!empty($this->{cn::GAME_PLANETS_IMAGE_COL})){
            $planet_image_url = env('APP_URL').$this->{cn::GAME_PLANETS_IMAGE_COL};
        }
        return $planet_image_url;
    }
}
