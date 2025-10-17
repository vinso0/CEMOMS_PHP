<?php
// controllers/admin/operations/garbage_collection/update.php

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

$truckId = $_POST['id'] ?? null;
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

$errors = [];

// Validation
if (empty($truckId)) {
    $errors[] = 'Invalid truck ID.';
}

if (empty($plateNumber)) {
    $errors[] = 'Plate number is required.';
} else {
    $truckModel = new Truck();
    if ($truckModel->plateNumberExists($plateNumber, $truckId)) {
        $errors[] = 'A truck with this plate number already exists.';
    }
}

if (empty($bodyNumber)) {
    $errors[] = 'Body number is required.';
}

if (empty($foremanId)) {
    $errors[] = 'Foreman is required.';
}

if (empty($routeName)) {
    $errors[] = 'Route name is required.';
}

if (empty($startPoint)) {
    $errors[] = 'Start point is required.';
}

if (empty($endPoint)) {
    $errors[] = 'End point is required.';
}

if (!in_array($scheduleType, ['daily', 'weekly'])) {
    $errors[] = 'Invalid schedule type.';
}

if (count($errors) === 0) {
    try {
        $truckModel = new Truck();
        $scheduleModel = new OperationSchedule();
        $routeModel = new Route();
        $operationModel = new Operation();
        
        // Check if truck exists
        $truck = $truckModel->getTruckById($truckId);
        if (!$truck) {
            $errors[] = 'Truck not found.';
        } else {
        // Update truck
        $truckModel->update($truckId, $plateNumber, $bodyNumber, $foremanId);
        
        // Update or create route
        if (!empty($routeId)) {
            // Update existing route
            $routeModel->updateRoute($routeId, $routeName, $startPoint, $midPoint, $endPoint);
        } else {
            // Create new route if doesn't exist
            $routeId = $routeModel->createRoute($routeName, $startPoint, $midPoint, $endPoint);
        }
        
        // Get the schedule for this truck
        $schedule = $scheduleModel->getByTruckId($truckId);
        
        if ($schedule) {
            // Update operation name if it exists
            if (!empty($schedule['operation_id'])) {  // ADD THIS BLOCK
                $operationName = "Garbage Collection - " . $plateNumber;
                $operationModel->update($schedule['operation_id'], $operationName, $bodyNumber);
            }
            
            // Keep the existing status - don't change it during update
            $scheduleModel->updateWithForeman(
                $schedule['schedule_id'],
                $routeId,
                $foremanId,
                $scheduleType,
                $schedule['status'] // Preserve current status
            );
        } else {
            // Create new operation and schedule if it doesn't exist
            $adminId = $_SESSION['user_id'] ?? 1;
            $operationName = "Garbage Collection - " . $plateNumber;
            $operationTypeId = 1; // Garbage Collection
            $operationId = $operationModel->create($operationName, $operationTypeId, $adminId, $bodyNumber);  // ADD THIS
            
            $areaId = 1; // Default area
            
            $scheduleModel->create(
                $operationId,  // CHANGED: use the operation ID
                $routeId,
                $areaId,
                $truckId,
                $adminId,
                $foremanId,
                $scheduleType,
                'Parked' // Default status for new schedules
            );
            }
            
            $_SESSION['success'] = 'Truck, route, and operation updated successfully.';
            header('Location: /admin/operations/garbage_collection');
            exit();
        }
    } catch (\Exception $e) {
        $errors[] = 'Error updating truck: ' . $e->getMessage();
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();