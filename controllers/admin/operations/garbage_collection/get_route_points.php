<?php
// controllers/admin/operations/garbage_collection/get_route_points.php

adminAuth();

use Models\Route;

require_once base_path('models/Route.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit('Method not allowed');
}

$routeId = $_GET['route_id'] ?? null;

if (!$routeId) {
    http_response_code(400);
    echo json_encode(['error' => 'Route ID is required']);
    exit();
}

try {
    $routeModel = new Route();
    $routePoints = $routeModel->getRoutePoints($routeId);
    
    // Format points for JavaScript
    $formattedPoints = [];
    foreach ($routePoints as $point) {
        $formattedPoints[] = [
            'name' => 'Point ' . $point['point_order'],
            'address' => 'Route point ' . $point['point_order'],
            'lat' => (float)$point['latitude'],
            'lng' => (float)$point['longitude'],
            'order' => (int)$point['point_order']
        ];
    }
    
    // Sort by order
    usort($formattedPoints, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    header('Content-Type: application/json');
    echo json_encode(['route_points' => $formattedPoints]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch route points: ' . $e->getMessage()]);
}
