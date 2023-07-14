<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Regions;
use App\Constants\DbConstant as cn;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $RegionsList = [
            [
                cn::REGIONS_REGION_EN_COL => "HK",
                cn::REGIONS_REGION_CH_COL => "香港",
                cn::REGIONS_STATUS_COL => 'active',
            ],
            [
                cn::REGIONS_REGION_EN_COL => "KLN",
                cn::REGIONS_REGION_CH_COL => "九龍",
                cn::REGIONS_STATUS_COL => 'active',
            ],
            [
                cn::REGIONS_REGION_EN_COL => "NT",
                cn::REGIONS_REGION_CH_COL => "新界",
                cn::REGIONS_STATUS_COL => 'active',
            ]
        ];
        foreach($RegionsList as $key => $Region){
            Regions::UpdateOrCreate($Region);
        }
    }
}
