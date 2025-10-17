<?php
// controllers/admin/operations/garbage_collection/index.php

adminAuth();

use Models\Truck;
use Models\Route;
use Models\Foreman;

require_once base_path('models/Truck.php');
require_once base_path('models/Route.php');
require_once base_path('models/Foreman.php');

try {
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

} catch (\Exception $e) {
    $_SESSION['errors'] = ['Error loading data: ' . $e->getMessage()];
    
    view('admin/operations/garbage_collection/index.view.php', [
        'trucks' => [],
        'routes' => [],
        'foremen' => [],
        'dispatch_logs' => []
    ]);
}