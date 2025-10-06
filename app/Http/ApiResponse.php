<?php

namespace App\Http;

class ApiResponse {

    public const HTTP_NO_STATUS = 0;
    // SUCCESS CODES
    public const HTTP_STATUS_OK = 200;
    public const HTTP_STATUS_CREATED = 201;
    public const HTTP_STATUS_NO_CONTENT = 204;

    // ERROR CODES
    public const HTTP_STATUS_BAD_REQUEST = 400;
    public const HTTP_STATUS_UNAUTHORIZED = 401;
    public const HTTP_STATUS_FORBIDDEN = 403;
    public const HTTP_STATUS_NOT_FOUND = 404;
    public const HTTP_STATUC_METHOD_NOT_ALLOWED = 405;

    // SERVER ERROR CODES
    public const HTTP_STATUS_SERVER_ERROR = 500;
    public const HTTP_STATUS_NOT_IMPLEMENTED = 501;
    public const HTTP_STATUS_SERVICE_NOT_AVAIL = 503;

    private static function sendDefaultHeaders():void {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
    }

    private static function sendStatusCode(int $code = self::HTTP_NO_STATUS, string $message = 'OK'):void {
        header("HTTP/1.1 $code $message");
    }

    private static function prepareResponse(?array $data, int $code = self::HTTP_NO_STATUS, string $message = ''):mixed {
        $response = [
            'api_version' => '1.0',
            'api_name' => 'punthenk_forum_api',
            'count' => count($data),
        ];

        if ($code !== 0)
            $response['status'] = $code;

        if (!empty($message))
            $response['status_message'] = $message;

        $response['data'] = $data;

        return json_encode($response);
    }

    public static function sendResponse(?array $data, int $code = self::HTTP_STATUS_OK, string $message = 'OK'):void {
        self::sendDefaultHeaders();
        self::sendStatusCode($code, $message);
        echo self::prepareResponse($data, $code, $message);
    }
}
