<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamePlanets;
use App\Constants\DbConstant as cn;

class GamePlanetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                cn:: GAME_PLANETS_NAME_COL => 'Earth',
                cn::GAME_PLANETS_IMAGE_COL => 'game_images\\Earth.jpg',
            ],
           
        ];
        foreach($data as $key => $value){
            GamePlanets::create($value);
        }
    }
}
