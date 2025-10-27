<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const BASE_PATH = __DIR__.'/../';

require BASE_PATH.'Core/functions.php';

spl_autoload_register(function ($class) {

    $class = str_replace('\\', '/', $class);

    require base_path("{$class}.php");
});

require base_path('bootstrap.php');

$router = new \Core\Router();
$routes = require base_path('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/admin/operations/garbage_collection/get_route_points' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require base_path('controllers/admin/operations/garbage_collection/get_route_points.php');
    exit;
}

if ($uri === '/api/geocode_proxy' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require base_path('controllers/api/geocode_proxy.php');
    exit;
}

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
$router->route($uri, $method);
