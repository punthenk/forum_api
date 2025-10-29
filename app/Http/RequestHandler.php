<?php

namespace App\Http;

use App\Http\ApiResponse;
use App\Models\UserModel;
use ArgumentCountError;
use Exception;

class RequestHandler {

    private const GET = 'GET';
    private const POST = 'POST';
    private const PATCH = 'PATCH';
    private const DELETE = 'DELETE';

    private $routes;
    private $recoures;
    private $httpRequestMethod;

    private $id;

    public function __construct(?array $routes) {
        $this->routes = $routes;
    }

    public function handle(): void {
        if (!isset($this->routes)) {
            ApiResponse::sendResponse(['error' => 'Routes not found'], ApiResponse::HTTP_STATUS_NOT_FOUND, 'Not found');
        }
        $this->setDataFromURI();
        $this->handleRequest();
    }

    private function checkValidRequest(): bool {
        // We call the findRoute method to find the controllerClass to use
        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        if ($classMethod === "login" || $classMethod === "register") {
            return true;
        }

        $token = $this->getAuthToken(); 

        if (UserModel::findByToken($token)) {
            return true;
        }

        return false;
    }

    private function getAuthToken(): ?string {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];

            if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }


    public function setDataFromURI():void {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim($uri, '/');

        // Set the http request type in a local variable
        $this->httpRequestMethod = $_SERVER['REQUEST_METHOD'];

        if ($uri === '') {
            $this->recoures = ['route' => '/', 'id' => null];
            ApiResponse::sendResponse(['message' => 'No specific route given'], ApiResponse::HTTP_STATUS_NO_CONTENT, 'NO CONTENT');
            die();
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
        // The http request (GET or POST for example) we send the BAD REQUEST status
        if (!isset($this->httpRequestMethod)) {
            ApiResponse::sendResponse(['error' => 'Not found'], ApiResponse::HTTP_STATUS_BAD_REQUEST, 'Not found');
            die();
        }

        $route = $this->recoures['route'];

        if (!isset($this->routes[$this->httpRequestMethod][$route])) {
            ApiResponse::sendResponse(
                ['error' => 'Route not found'],
                ApiResponse::HTTP_STATUS_NOT_FOUND,
                'Not Found'
            );
            die();
        }

        // We find the controller route here and return it
        $controllerRoute = $this->routes[$this->httpRequestMethod][$route];
        return $controllerRoute;
    }

    private function handleGetRequest():void {

        // We call the findRoute method to find the controllerClass to use
        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        // This is the new instance of the controller
        $controller = new $className;

        try {
            // If the id is specified we will call the find method to find row with that id
            // If there is no id than we just call the index method to get all the data
            if ($this->id !== 0 && $this->id !== null) {
                $responseValue = $controller->$classMethod($this->id);
            } else {
                $responseValue = $controller->$classMethod();
            }

            // If we did not get any data we send the NOT FOUND status
            if (empty($responseValue)) {
                ApiResponse::sendResponse(['error' => 'No data found'], ApiResponse::HTTP_STATUS_NOT_FOUND, 'Resource not found');
                die();
            }

            ApiResponse::sendResponse($responseValue);
            die();

            // If we catch an argument error that we called the index method but we dont get an id
            // we send the BAD REQUEST status
        } catch (ArgumentCountError $e) {
            ApiResponse::sendResponse(
                ['error' => 'ID parameter is required for this endpoint'],
                ApiResponse::HTTP_STATUS_BAD_REQUEST,
                'Bad Request'
            );
        }
    }

    private function handlePostRequest():void {

        // We call the findRoute method to find the controllerClass to use
        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        // This is the new instance of the controller
        $controller = new $className;

        $data = $_POST;

        // We check here if all the required data is'nt empty
        foreach ($data as $obj) {
            if (!isset($obj) || empty($obj)) {
                // If one property is empty we give a status NOT FOUND
                ApiResponse::sendResponse(['error' => 'Not all data found'], ApiResponse::HTTP_STATUS_NOT_FOUND, 'NOT ALL DATA');
                die();
            }
        }

        if ($data !== null && !empty($data)) {
            $responseValue = $controller->$classMethod($data);
        } else {
            ApiResponse::sendResponse(['error' => 'No data found'], ApiResponse::HTTP_STATUS_NOT_FOUND, 'NO DATA');
            die();
        }

        // We send the API response here
        ApiResponse::sendResponse($responseValue, ApiResponse::HTTP_STATUS_CREATED, 'CREATED');
        die();
    }

    private function handlePatchRequest():void {

        // We call the findRoute method to find the controllerClass to use
        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];


        // This is the new instance of the controller
        $controller = new $className;

        // Here we get the raw json data
        $jsonData = file_get_contents('php://input');
        // We turn the json data into an array
        $data = json_decode($jsonData, true);

        try {
            if ($data !== null && !empty($data)) {
                $responseValue = $controller->$classMethod($data);
            } else {
                ApiResponse::sendResponse(['error' => 'No id given'], ApiResponse::HTTP_STATUS_BAD_REQUEST, 'NO ID FOUND');
                die();
            }

            // We send the API response here
            ApiResponse::sendResponse($responseValue);
            die();
        } catch (Exception $e) {
            ApiResponse::sendResponse(
                ['error' => $e->getMessage()], 
                ApiResponse::HTTP_STATUS_BAD_REQUEST, 
                'Update Failed'
            );           
            die();
        }


    }

    private function handleDeleteRequest():void {

        // We call the findRoute method to find the controllerClass to use
        $controllerClass = $this->findRoute();

        // This is the classname
        $className = $controllerClass[0];
        // This is the method name that needs to be called
        $classMethod = $controllerClass[1];

        // This is the new instance of the controller
        $controller = new $className;

        // We try to delete the row in the DB
        // If we catch an error we will give a 400 response
        try {
            if ($this->id !== 0 && $this->id !== null && gettype($this->id) === "integer") {
                //We exectute the method here
                $responseValue = $controller->$classMethod($this->id);
            } else {
                ApiResponse::sendResponse([], ApiResponse::HTTP_STATUS_BAD_REQUEST, 'NO ID FOUND');
                die();
            } 

            // We send the API response here
            ApiResponse::sendResponse($responseValue);
            die();
        } catch (Exception $e) {
            ApiResponse::sendResponse(
                ['error' => $e->getMessage()], 
                ApiResponse::HTTP_STATUS_BAD_REQUEST, 
                'Delete Failed'
            );           
            die();
        }

    }

    private function handleRequest():void {

        if ($this->httpRequestMethod !== RequestHandler::GET) {
            if ($this->checkValidRequest() === false) {
                ApiResponse::sendResponse(['error' => 'Not authorized'], ApiResponse::HTTP_STATUS_UNAUTHORIZED, 'UNAUTHORIZED');
                die();
            }
        }

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

}
