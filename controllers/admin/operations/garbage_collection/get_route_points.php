<?php
// controllers/admin/operations/garbage_collection/get_route_points.php

adminAuth();

header('Content-Type: application/json; charset=utf-8'); 

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
    $routeDetails = $routeModel->getRouteById($routeId);
    
    if (!$routeDetails) {
        throw new Exception('Route not found');
    }
    
    // Format points using the EXACT stored addresses
    $formattedPoints = [];
    
    foreach ($routePoints as $point) {
        $lat = (float)$point['latitude'];
        $lng = (float)$point['longitude'];
        $order = (int)$point['point_order'];
        
        // Map point order to the exact stored addresses
        switch ($order) {
            case 1:
                $pointName = 'Start Point';
                $address = $routeDetails['start_point'] ?: 'Starting location';
                break;
                
            case 2:
                if (!empty($routeDetails['mid_point'])) {
                    // This is actually the mid point
                    $pointName = 'Collection Point';
                    $address = $routeDetails['mid_point'];
                } else {
                    // No mid point, so this is the end point
                    $pointName = 'End Point';
                    $address = $routeDetails['end_point'] ?: 'Final destination';
                }
                break;
                
            case 3:
                // This is definitely the end point (when mid exists)
                $pointName = 'End Point';
                $address = $routeDetails['end_point'] ?: 'Final destination';
                break;
                
            default:
                $pointName = 'Route Point';
                $address = 'Point ' . $order;
                break;
        }
        
        $formattedPoints[] = [
            'name' => $pointName,
            'address' => $address,
            'lat' => $lat,
            'lng' => $lng,
            'order' => $order
        ];
    }
    
    // Sort by order
    usort($formattedPoints, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    echo json_encode(['route_points' => $formattedPoints]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch route points: ' . $e->getMessage()]);
}
