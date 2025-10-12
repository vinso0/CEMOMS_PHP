<?php
adminAuth();

use Models\Route;
use Models\Truck;

require_once base_path('models/Route.php');
require_once base_path('models/Truck.php');

$routeModel = new Route();
$truckModel = new Truck();

$routes = $routeModel->getAllRoutes();
$trucks = $truckModel->getAllTrucks();

require base_path('views/admin/operations/garbage_collection/create.php');