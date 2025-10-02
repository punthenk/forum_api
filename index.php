<?php

@include('vendor/autoload.php');

$routes = @include('app/Routes/routes.php');

$requestHandler = new App\Http\RequestHandler($routes);

$requestHandler->handle();
