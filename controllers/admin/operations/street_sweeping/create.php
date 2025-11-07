<?php
// controllers/admin/operations/street_sweeping/create.php

adminAuth();

use Models\Route;
use Models\Foreman;

require_once base_path('models/Route.php');
require_once base_path('models/Foreman.php');

try {
    $routeModel = new Route();
    $foremanModel = new Foreman();

    // Get street sweeping specific data (no trucks for street sweeping)
    $routes = $routeModel->getAllRoutes();
    $foremen = $foremanModel->getStreetSweepingForemen(); // Only foremen with Street Sweeping role

    // Return data as JSON for modal
    header('Content-Type: application/json');
    echo json_encode([
        'routes' => $routes,
        'foremen' => $foremen
    ]);

} catch (\Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    error_log('Error loading street sweeping create data: ' . $e->getMessage());
}
