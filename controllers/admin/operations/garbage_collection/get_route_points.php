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
    
    // Also get the route details for better names
    $routeDetails = $routeModel->getRouteById($routeId);
    
    // Format points for JavaScript with meaningful names
    $formattedPoints = [];
    foreach ($routePoints as $point) {
        $lat = (float)$point['latitude'];
        $lng = (float)$point['longitude'];
        $order = (int)$point['point_order'];
        
        // Determine point type and use route's stored addresses
        if ($order === 1) {
            $pointName = 'Start Point';
            $address = $routeDetails['start_point'] ?? 'Starting location';
        } elseif ($order === 2 && !empty($routeDetails['mid_point'])) {
            $pointName = 'Collection Point';
            $address = $routeDetails['mid_point'] ?? 'Collection area';
        } else {
            $pointName = 'End Point';
            $address = $routeDetails['end_point'] ?? 'Final destination';
        }
        
        // If address is empty or too generic, create a descriptive one
        if (empty($address) || $address === 'Route point ' . $order) {
            $address = getSimpleLocationName($lat, $lng, $pointName);
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

/**
 * Generate a simple location name based on coordinates
 */
function getSimpleLocationName($lat, $lng, $pointType) {
    // Philippines coordinate ranges for basic area detection
    if ($lat >= 14.4 && $lat <= 14.8 && $lng >= 120.9 && $lng <= 121.1) {
        // Metro Manila area
        if ($lat >= 14.55 && $lat <= 14.65) {
            $area = "Manila City";
        } elseif ($lat >= 14.5 && $lat < 14.55) {
            $area = "Pasay/Makati Area";
        } elseif ($lat > 14.65) {
            $area = "Quezon City Area";
        } else {
            $area = "Metro Manila";
        }
    } else {
        $area = "Philippines";
    }
    
    return $pointType . " - " . $area;
}