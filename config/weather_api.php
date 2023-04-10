<?php

return [
    'host' => env('WEATHER_API_HOST', 'https://data.weather.gov.hk/'),
    'api' => [
        'weather_detail' => [
            'uri' => 'weatherAPI/opendata/weather.php',
            'method' => 'GET',
            'dataType' => 'rhrread',
            'lang' => 'en'
        ],
    ]
];
