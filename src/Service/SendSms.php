<?php 

namespace App\Service;

class SendSms
{

    function CallAPI($method, $url, $data = false)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "$method",
            CURLOPT_POSTFIELDS =>"$data",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic am9zaWFoYmlyYWk6RWlyd21yLTMz"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }


}