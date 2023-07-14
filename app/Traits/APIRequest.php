<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait APIRequest
{
    public function GET_Request($URL)
    {
        $Response = array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $URL,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $resp = curl_exec($curl);
        $Response = (array) json_decode($resp);
        return $Response;
    }
}