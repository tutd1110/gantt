<?php

namespace App\Helper;

use GuzzleHttp\Client;


class Mycurl
{
    const TIME_OUT = 120;

    public static function postCurl($url, $access_token = "", $params = [])
    {
        try {

            $optionClient = array(
                'timeout' => self::TIME_OUT,
                'verify' => false
            );

            $client = new Client($optionClient);
            $headers = [];
            if($access_token){
                $headers['Authorization'] = 'Bearer ' . $access_token;
            }

            $response = $client->post($url, [
                'headers' => $headers,
                'form_params' => $params
            ]);
            $content = $response->getBody()->getContents();

            $result = json_decode($content, true);

            return $result;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function putCurl($url, $access_token, $params = [] )
    {
        try {

            $optionClient = array(
                'timeout' => self::TIME_OUT,
                'verify' => false
            );

            $client = new Client($optionClient);
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];

            $response = $client->put($url, [
                'headers' => $headers,
                'form_params' => $params
            ]);
            $content = $response->getBody()->getContents();

            $result = json_decode($content, true);

            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function deleteCurl($url, $access_token, $params = [])
    {
        try {

            $optionClient = array(
                'timeout' => self::TIME_OUT,
                'verify' => false
            );

            $client = new Client($optionClient);
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];

            $response = $client->delete($url, [
                'headers' => $headers,
                'form_params' => $params
            ]);
            $content = $response->getBody()->getContents();

            $result = json_decode($content, true);

            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getCurl($url, $access_token, $params = [])
    {
        try {
            $optionClient = array(
                'verify' => false
            );

            $client = new Client($optionClient);
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
            ];
            $response = $client->get($url,
                [
                    'headers' => $headers,
                    'query' => $params
                ]
            );

            $content = $response->getBody()->getContents();

            $result = json_decode($content, true);

            return $result;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


}
