<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: Curl.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Utilities;

class Curl {

    //Standard curl post request
    function curlPost($postdata, $url) {

        $jsonrequest = json_encode($postdata);

        $headers = array();
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Keep-Alive: 300';
        $headers[] = 'Content-Encoding: gzip';
        $headers[] = 'Content-type: application/json;charset=UTF-8';


        $curl = curl_init();

        curl_setopt_array(
                $curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FAILONERROR => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTFIELDS => $jsonrequest));
        $response = curl_exec($curl);

        if (!$response) {
            Log::error('Curl_Exec_Error ->' . curl_error($curl) . ' (' . curl_errno($curl) . ')');
        }

        $response = json_decode($response);

        curl_close($curl);

        return $response;
    }

    //Standard curl get request
    function curlGet($url) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }

    function curlPut($url, $data) {

        $data = json_encode($data);

        $headers = array();
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Keep-Alive: 300';
        $headers[] = 'Content-Encoding: gzip';
        $headers[] = 'Content-type: application/json;charset=UTF-8';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }

}

?>
