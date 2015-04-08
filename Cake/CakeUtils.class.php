<?php

namespace Cake;

class CakeUtils
{

    private function __construct() {} //singelton

    private static function getCurlResponse($url, $options = [CURLOPT_RETURNTRANSFER => true])
    {

        $curl = curl_init();

        curl_setopt(CURLOPT_URL, $url);
        curl_setopt_array($curl, $options);

        $result = curl_exec($curl);
        return json_decode($result, true);
    }

}
