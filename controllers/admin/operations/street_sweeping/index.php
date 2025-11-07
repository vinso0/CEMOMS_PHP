<?php
// controllers/admin/operations/street_sweeping/index.php

adminAuth();

use Models\Route;
use Models\Foreman;
use Models\StreetSweeping;

require_once base_path('models/Route.php');
require_once base_path('models/Foreman.php');
require_once base_path('models/StreetSweeping.php');

try {
    $streetSweepingModel = new StreetSweeping();
    $routeModel = new Route();
    $foremanModel = new Foreman();

    // Get street sweeping specific data
    $routes = $routeModel->getAllRoutes();
    $foremen = $foremanModel->getStreetSweepingForemen(); // Only foremen with role_id = 2
    $street_sweeping_schedules = $streetSweepingModel->getRecentSchedules(10);

    view('admin/operations/street_sweeping/index.view.php', [
        'routes' => $routes,
        'foremen' => $foremen,
        'street_sweeping_schedules' => $street_sweeping_schedules
    ]);

} catch (\Exception $e) {
    $_SESSION['errors'] = ['Error loading data: ' . $e->getMessage()];
    
    view('admin/operations/street_sweeping/index.view.php', [
        'routes' => [],
        'foremen' => [],
        'street_sweeping_schedules' => []
    ]);
}
