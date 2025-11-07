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

    // Pagination parameters
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 10; // Items per page
    $offset = ($page - 1) * $perPage;

    // Get only garbage collection trucks (operation_type_id = 1) with pagination
    $trucks = $truckModel->getGarbageCollectionTrucks($perPage, $offset);
    $totalTrucks = $truckModel->getGarbageCollectionTrucksCount();
    $totalPages = ceil($totalTrucks / $perPage);

    $routes = $routeModel->getAllRoutes();
    $foremen = $foremanModel->getGarbageCollectionForemen(); // Only foremen with role_id = 1
    $dispatch_logs = $truckModel->getGarbageCollectionDispatchLogs(10);

    view('admin/operations/garbage_collection/index.view.php', [
        'trucks' => $trucks,
        'routes' => $routes,
        'foremen' => $foremen,
        'dispatch_logs' => $dispatch_logs,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalTrucks' => $totalTrucks,
        'perPage' => $perPage
    ]);

} catch (\Exception $e) {
    $_SESSION['errors'] = ['Error loading data: ' . $e->getMessage()];

    view('admin/operations/garbage_collection/index.view.php', [
        'trucks' => [],
        'routes' => [],
        'foremen' => [],
        'dispatch_logs' => [],
        'currentPage' => 1,
        'totalPages' => 1,
        'totalTrucks' => 0,
        'perPage' => 10
    ]);
}