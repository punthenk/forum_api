<?php
@include('vendor/autoload.php');

/* $routes = @include('app/Routes/routes.php'); */

/* $requestHandler = new App\Http\RequestHandler($routes); */

use App\Models\ThreadModel;

$data = $_POST ?? null;

$data = ThreadModel::create($data);
echo '<pre>';
print_r($data);

