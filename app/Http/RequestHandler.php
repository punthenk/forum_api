<?php

namespace App\Http;

class RequestHandler {

    private const GET = 'GET';

    private $request_type;

    private function handleGetRequest() {
        echo 'getrequest'; 
    }

    public function handleRequest():void {
        switch ($this->request_type) {
            case RequestHandler::GET:
                $this->handleGetRequest();
                break;
            default:
                echo "hello";
                break;
        }
    }
}
