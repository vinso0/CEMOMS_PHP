<?php

adminAuth();

use Models\Truck;
use Models\Route;
use Models\OperationSchedule;
use Models\Operation;

require_once base_path('models/Truck.php');
require_once base_path('models/OperationSchedule.php');
require_once base_path('models/Route.php');
require_once base_path('models/Operation.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/garbage_collection');
    exit();
}

// Fixed parameter name from 'id' to 'truck_id'
$truckId = isset($_POST['truck_id']) && is_numeric($_POST['truck_id']) ? (int)$_POST['truck_id'] : null;
$plateNumber = trim($_POST['plate_number'] ?? '');
$bodyNumber = trim($_POST['body_number'] ?? '');
$foremanId = $_POST['foreman_id'] ?? null;
$scheduleType = $_POST['schedule_type'] ?? 'daily';

// Route data
$routeId = $_POST['route_id'] ?? null;
$routeName = trim($_POST['route_name'] ?? '');
$startPoint = trim($_POST['start_point'] ?? '');
$midPoint = trim($_POST['mid_point'] ?? '');
$endPoint = trim($_POST['end_point'] ?? '');

// Coordinate data
$startLat = $_POST['start_lat'] ?? null;
$startLon = $_POST['start_lon'] ?? null;
$midLat = $_POST['mid_lat'] ?? null;
$midLon = $_POST['mid_lon'] ?? null;
$endLat = $_POST['end_lat'] ?? null;
$endLon = $_POST['end_lon'] ?? null;

$errors = [];

// Enhanced validation with better error messages
if (empty($truckId) || !is_numeric($truckId)) {
    $errors[] = 'Invalid truck ID provided.';
} else {
    // Verify truck exists before proceeding
    $truckModel = new Truck();
    $existingTruck = $truckModel->getTruckById($truckId);
    if (!$existingTruck) {
        $errors[] = 'Truck not found in the system.';
    }
}

if (empty($plateNumber)) {
    $errors[] = 'Plate number is required.';
} elseif (strlen($plateNumber) > 20) {
    $errors[] = 'Plate number cannot exceed 20 characters.';
} elseif (!preg_match('/^[A-Za-z0-9 -]+$/', $plateNumber)) {
    $errors[] = 'Plate number can only contain letters, numbers, spaces, and hyphens.';
} else {
    // Only check for duplicates if truck ID is valid
    if (!empty($truckId) && is_numeric($truckId)) {
        $truckModel = new Truck();
        if ($truckModel->plateNumberExists($plateNumber, $truckId)) {
            $errors[] = 'A truck with this plate number already exists.';
        }
    }
}

if (empty($bodyNumber)) {
    $errors[] = 'Body number is required.';
} elseif (strlen($bodyNumber) > 20) {
    $errors[] = 'Body number cannot exceed 20 characters.';
} elseif (!preg_match('/^[A-Za-z0-9-]+$/', $bodyNumber)) {
    $errors[] = 'Body number can only contain letters, numbers, and hyphens.';
}

if (empty($foremanId) || !is_numeric($foremanId)) {
    $errors[] = 'Valid foreman selection is required.';
}

if (empty($routeName)) {
    $errors[] = 'Route name is required.';
} elseif (strlen($routeName) > 100) {
    $errors[] = 'Route name cannot exceed 100 characters.';
}

if (empty($startPoint)) {
    $errors[] = 'Start point is required.';
}

if (empty($endPoint)) {
    $errors[] = 'End point is required.';
}

// Validate coordinates with better error messages
if (empty($startLat) || empty($startLon) || !is_numeric($startLat) || !is_numeric($startLon)) {
    $errors[] = 'Start point coordinates are invalid. Please select a valid location on the map.';
}

if (empty($endLat) || empty($endLon) || !is_numeric($endLat) || !is_numeric($endLon)) {
    $errors[] = 'End point coordinates are invalid. Please select a valid location on the map.';
}

// Validate mid point coordinates if mid point address is provided
if (!empty($midPoint) && (empty($midLat) || empty($midLon) || !is_numeric($midLat) || !is_numeric($midLon))) {
    $errors[] = 'Mid point coordinates are invalid. Please select a valid location on the map for the mid point.';
}

if (!in_array($scheduleType, ['daily', 'weekly'])) {
    $errors[] = 'Invalid schedule type selected.';
}

// If weekly schedule, validate that at least one day is selected
if ($scheduleType === 'weekly') {
    $scheduleDays = $_POST['schedule_days'] ?? [];
    if (empty($scheduleDays) || !is_array($scheduleDays)) {
        $errors[] = 'At least one day must be selected for weekly schedule.';
    }
}

// Continue with the rest of the existing update logic...
if (count($errors) === 0) {
    try {
        $truckModel = new Truck();
        $scheduleModel = new OperationSchedule();
        $routeModel = new Route();
        $operationModel = new Operation();
        
        // Update truck
        $truckModel->update($truckId, $plateNumber, $bodyNumber, $foremanId);
        
        // Update or create route with coordinates
        if (!empty($routeId)) {
            $routeModel->updateRouteWithCoordinates(
                $routeId,
                $routeName,
                $startPoint, $startLat, $startLon,
                $midPoint, $midLat, $midLon,
                $endPoint, $endLat, $endLon
            );
        } else {
            $routeId = $routeModel->createRouteWithCoordinates(
                $routeName,
                $startPoint, $startLat, $startLon,
                $midPoint, $midLat, $midLon,
                $endPoint, $endLat, $endLon
            );
        }
        
        // Get the schedule for this truck
        $schedule = $scheduleModel->getByTruckId($truckId);
        
        if ($schedule) {
            // Update operation name if it exists
            if (!empty($schedule['operation_id'])) {
                $operationName = "Garbage Collection - " . $plateNumber;
                $operationModel->update($schedule['operation_id'], $operationName, $bodyNumber);
            }
            
            // Keep the existing status - don't change it during update
            $scheduleModel->updateWithForeman(
                $schedule['schedule_id'],
                $routeId,
                $foremanId,
                $scheduleType,
                $schedule['status']
            );
        } else {
            // Create new operation and schedule if it doesn't exist
            $adminId = $_SESSION['user_id'] ?? 1;
            $operationName = "Garbage Collection - " . $plateNumber;
            $operationTypeId = 1;
            $operationId = $operationModel->create($operationName, $operationTypeId, $adminId, $bodyNumber);
            
            $areaId = 1;
            
            $scheduleModel->create(
                $operationId,
                $routeId,
                $areaId,
                $truckId,
                $adminId,
                $foremanId,
                $scheduleType,
                'Parked'
            );
        }
        
        $_SESSION['success'] = 'Truck updated successfully with all route information.';
        header('Location: /admin/operations/garbage_collection');
        exit();
        
    } catch (\Exception $e) {
        $errors[] = 'Database error occurred while updating truck: ' . $e->getMessage();
        error_log("Truck update error for ID {$truckId}: " . $e->getMessage());
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();
