<?php

namespace App\Http;

use App\Http\ApiResponse;

class RequestHandler {

    private const GET = 'GET';
    private const POST = 'POST';
    private const PUT = 'PUT';
    private const PATCH = 'PATCH';
    private const DELETE = 'DELETE';

    private $routes;
    private $recoures;
    private $httpRequestMethod;
    private $parseUrl;

    private $id;


    public function __construct(?array $routes) {
        $this->routes = $routes;
    }

    public function handle(): void {
        if (!isset($this->routes)) {
            $this->handle404();
        }

        $this->setDataFromURI();
        $this->handleRequest();

    }

    public function setDataFromURI():void {

        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim($uri, '/');

        // Set the http request type in a local variable
        $this->httpRequestMethod = $_SERVER['REQUEST_METHOD'];

        if ($uri === '') {
            $this->recoures = ['route' => '/', 'id' => null];
        }


        $parts = explode('/', $uri);
        $route = $parts[0];
        $id = isset($parts[1]) ? $parts[1] : null;

        // This cuts the URL into a usable array
        $this->recoures =  ['route' => $route, 'id' => $id];
        $this->id = $id;

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $this->id = (int) $_POST['id'];
        }
    }

    public function findRoute():?array {

        if (!isset($this->httpRequestMethod)) {
            ApiResponse::sendResponse(['message' => 'Not found'], ApiResponse::HTTP_STATUS_BAD_REQUEST, 'Not found');
            die();
        }

        $route = $this->recoures['route'];

        $controllerRoute = $this->routes[$this->httpRequestMethod][$route];
        return $controllerRoute;
    }

    private function handleGetRequest():void {

        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;

        if ($this->id !== 0 && $this->id !== null) {
            $responseValue = $controller->$classMethod($this->id);
        } else {
            $responseValue = $controller->$classMethod();
        }

        // We send the API resopse here
        ApiResponse::sendResponse($responseValue);
        die();
    }

    private function handlePostRequest():void {

        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;

        $data = $_POST;

        if ($data !== null && !empty($data)) {
            $responseValue = $controller->$classMethod($data);
        }

        // We send the API response here
        ApiResponse::sendResponse($responseValue);
        die();
    }

    private function handlePatchRequest():void {

        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if ($data !== null && !empty($data)) {
            $responseValue = $controller->$classMethod($data);
        } else {
            ApiResponse::sendResponse([], ApiResponse::HTTP_STATUS_BAD_REQUEST, 'NO ID FOUND');
            die();
        }

        // We send the API response here
        ApiResponse::sendResponse($responseValue);
        die();
    }

    private function handleDeleteRequest():void {

        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;

        if ($this->id !== 0 && $this->id !== null) {
            $responseValue = $controller->$classMethod($this->id);
        } else {
            ApiResponse::sendResponse([], ApiResponse::HTTP_STATUS_BAD_REQUEST, 'NO ID FOUND');
            die();
        } 

        // We send the API response here
        ApiResponse::sendResponse($responseValue);
        die();

    }

    private function handleRequest():void {

        switch ($this->httpRequestMethod) {
            case RequestHandler::GET:
                $this->handleGetRequest();
                break;
            case RequestHandler::POST:
                $this->handlePostRequest();
                break;
            case RequestHandler::PATCH:
                $this->handlePatchRequest();
                break;
            case RequestHandler::DELETE:
                $this->handleDeleteRequest();
                break;
            
            default:
                $this->handleGetRequest();
                break;
        }
    }

    private function handle404(): void {
        echo "This is not good";
    }

}
