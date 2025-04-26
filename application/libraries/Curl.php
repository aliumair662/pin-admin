<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Curl {

    // Function to perform GET request
    public function simple_get($url, $headers = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional: Disable SSL verification if needed

        // Add headers if provided
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        if(curl_errno($ch)) {
            // Handle the error if needed
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        return $response;
    }

    // Function to perform POST request
    public function simple_post($url, $data = array(), $headers = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional: Disable SSL verification if needed

        // Add POST data
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // Add headers if provided
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        if(curl_errno($ch)) {
            // Handle the error if needed
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        return $response;
    }
}