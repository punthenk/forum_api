<?php

namespace App\Http;

class RequestHandler {

    private const GET = 'GET';
    private const POST = 'POST';
    private const PUT = 'PUT';
    private const PATCH = 'PATCH';

    private $routes;
    private $recoures;
    private $httpRequestMethod;
    private $parseUrl;

    private $id;

    public function __construct($routes) {
        $this->routes = $routes;
    }

    public function handle(): void {
        if (!isset($this->routes)) {
            $this->handle404();
        }

        $this->getCleanURI();
        $this->handleRequest();

    }

    public function getCleanURI():void {

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

    public function findRoute(string $httpRequestType, ?array $uriData):?array {

        if (!isset($this->routes[$httpRequestType])) {
            return null;
        }

        $route = $uriData['route'];
        /* $id = $uriData['id']; */

        $controllerRoute = $this->routes[$httpRequestType][$route];
        return $controllerRoute;
    }

    private function executeController($controllerClass, $method, $id = null):bool {
        $controller = new $controllerClass;
        /* var_dump($controller, $method); */
        
        if (!method_exists($controller, $method)) {
            echo "The method does not exist";
            return false;
        }

        if ($id !== null) {
            $controller->$method($id);
        } else {
            $controller->$method();
        }

        return true;
    }

    private function handleGetRequest():void {

        $controllerClass = $this->findRoute($this->httpRequestMethod, $this->recoures);

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;
        var_dump($controller);
        print_r($this->recoures['id']);
        print_r($this->recoures['route']);

        if ($this->id !== 0 && $this->id !== null) {
            $responeValue = $controller->$classMethod($this->id);
        } else {
            $responeValue = $controller->$classMethod();
        }

        // We send the API resopse here
        print_r($responeValue);

    }

    private function handlePostRequest():void {

        $controllerClass = $this->findRoute($this->httpRequestMethod, $this->recoures);

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;

        $data = $_POST;
        var_dump($classMethod);

        if ($data !== null && !empty($data)) {
            $responseValue = $controller->$classMethod($data);
        }

        // We send the API response here
        print_r($responseValue);
    }

    private function handlePatchRequest():void {

        $controllerClass = $this->findRoute($this->httpRequestMethod, $this->recoures);

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        $controller = new $className;

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        var_dump($jsonData);
        var_dump($data);

        if ($data !== null && !empty($data)) {
            $responseValue = $controller->$classMethod($data);
        }

        // We send the API response here
        print_r($responseValue);
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
            
            default:
                $this->handleGetRequest();
                break;
        }
    }

    private function handle404(): void {
        echo "This is not good";
    }

}
