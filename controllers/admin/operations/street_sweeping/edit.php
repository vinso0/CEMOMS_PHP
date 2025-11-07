<?php
// controllers/admin/operations/street_sweeping/edit.php

adminAuth();

use Models\StreetSweeping;
use Models\Route;
use Models\Foreman;

require_once base_path('models/StreetSweeping.php');
require_once base_path('models/Route.php');
require_once base_path('models/Foreman.php');

$scheduleId = $_GET['id'] ?? null;

if (!$scheduleId) {
    $_SESSION['errors'] = ['Schedule ID is required'];
    header('Location: /admin/operations/street_sweeping');
    exit();
}

try {
    $streetSweepingModel = new StreetSweeping();
    $routeModel = new Route();
    $foremanModel = new Foreman();

    $schedule = $streetSweepingModel->getById($scheduleId);
    $routes = $routeModel->getAllRoutes();
    $foremen = $foremanModel->getStreetSweepingForemen();

    if (!$schedule) {
        $_SESSION['errors'] = ['Schedule not found'];
        header('Location: /admin/operations/street_sweeping');
        exit();
    }

    // Get route points
    $routePoints = $routeModel->getRoutePoints($schedule['route_id']);

    // Return data as JSON for modal
    header('Content-Type: application/json');
    echo json_encode([
        'schedule' => $schedule,
        'routes' => $routes,
        'foremen' => $foremen,
        'route_points' => $routePoints
    ]);

} catch (\Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
