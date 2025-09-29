<?php

namespace App\Http;

class ApiResponse {

    public const HTTP_NO_STATUS = 0;
    public const HTTP_STATUS_OK = 200;

    public const HTTP_STATUS_BAD_REQUEST = 400;

    private static function sendDefaultHeaders():void {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
    }

    private static function sendStatusCode($code = self::HTTP_NO_STATUS, $message = 'OK') {
        header("HTTP/1.1 $code $message");
    }

    private static function prepareResponse($data, $code = self::HTTP_NO_STATUS, $message = ''):mixed {
        $response = [
            'api_version' => '0.5',
            'api_name' => 'forum_api',
            'count' => count($data),
        ];

        if ($code !== 0)
            $response['status'] = $code;

        if (!empty($message))
            $response['status_message'] = $message;

        $response['data'] = $data;

        return json_encode($response);
    }

    public static function sendResponse($data, $code = self::HTTP_STATUS_OK, $message = 'OK') {
        self::sendDefaultHeaders();
        self::sendStatusCode($code, $message);
        echo self::prepareResponse($data, $code, $message);
    }
}
