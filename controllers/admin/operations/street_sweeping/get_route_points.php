<?php
// controllers/admin/operations/street_sweeping/get_route_points.php

adminAuth();

use Models\Route;

require_once base_path('models/Route.php');

// Set JSON header
header('Content-Type: application/json');

// Get route ID from request
$routeId = $_GET['route_id'] ?? null;

if (empty($routeId) || !is_numeric($routeId)) {
    echo json_encode([
        'success' => false,
        'error' => 'Valid route ID is required'
    ]);
    exit();
}

try {
    $routeModel = new Route();
    
    // Get route details
    $route = $routeModel->getRouteById($routeId);
    
    if (!$route) {
        echo json_encode([
            'success' => false,
            'error' => 'Route not found'
        ]);
        exit();
    }
    
    // Get route points (coordinates)
    $routePoints = $routeModel->getRoutePoints($routeId);
    
    // Format response
    $response = [
        'success' => true,
        'route' => $route,
        'points' => $routePoints
    ];
    
    echo json_encode($response);
    
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error fetching route points: ' . $e->getMessage()
    ]);
    
    // Log error for debugging
    error_log('Error fetching route points: ' . $e->getMessage());
}
