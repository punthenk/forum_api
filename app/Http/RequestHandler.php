<?php

namespace App\Http;

class RequestHandler {

    private const GET = 'GET';
    private const POST = 'POST';
    private const PUT = 'PUT';

    private $routes;
    private $recoures;

    public function __construct($routes) {
        $this->routes = $routes;

    }

    public function getCleanURI():?array {

        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim($uri, '/');

        if ($uri === '') {
            return ['route' => '/', 'id' => null];
        }

        $parts = explode('/', $uri);
        $route = $parts[0];
        $id = isset($parts[1]) ? $parts[1] : null;

        $return = ['route' => $route, 'id' => $id];
        print_r($return);

        return ['route' => $route, 'id' => $id];
    }

    public function findRoute(string $method, string $uri):?array {

        if (!isset($this->routes[$method])) {
            return null;
        }
    }
}
