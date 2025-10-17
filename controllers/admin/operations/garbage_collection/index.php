<?php

adminAuth();

use Models\Truck;
use Models\Route;
use Models\Foreman;

$truckModel = new Truck();
$routeModel = new Route();
$foremanModel = new Foreman();

// Get all data
$trucks = $truckModel->getAllTrucks();
$routes = $routeModel->getAllRoutes();
$foremen = $foremanModel->findAll();
$dispatch_logs = $truckModel->getDispatchLogs(10);

view('admin/operations/garbage_collection/index.view.php', [
    'trucks' => $trucks,
    'routes' => $routes,
    'foremen' => $foremen,
    'dispatch_logs' => $dispatch_logs
]);