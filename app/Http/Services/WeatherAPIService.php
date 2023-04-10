<?php

namespace App\Http\Services;

use App\Traits\APIRequest;
use App\Traits\Common;
use Illuminate\Support\Facades\Log;

class WeatherAPIService
{
    use APIRequest, Common;

    protected $host = null;
    protected $url = null;
    protected $method = null;

    public function __construct(){
        $this->host = config()->get('weather_api.host');
    }

    public function GetWeatherInfo(){
        $this->url = config()->get('weather_api.api.weather_detail.uri');
        $requestUrl = $this->host.$this->url;
        $requestUrl = $requestUrl.'?dataType='.config()->get('weather_api.api.weather_detail.dataType').'&lang='.config()->get('weather_api.api.weather_detail.lang');
        $result = $this->GET_Request($requestUrl);
        return $result;
    }
}