<?php
// controllers/admin/operations/garbage_collection/update.php

adminAuth();

use Models\Truck;
use Models\Route;
use Models\OperationSchedule;

require_once base_path('models/Truck.php');
require_once base_path('models/OperationSchedule.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/garbage_collection');
    exit();
}

$truckId = $_POST['id'] ?? null;
$plateNumber = trim($_POST['plate_number'] ?? '');
$bodyNumber = trim($_POST['body_number'] ?? '');
$foremanId = $_POST['foreman_id'] ?? null;
$routeId = $_POST['route_id'] ?? null;
$scheduleType = $_POST['schedule_type'] ?? 'daily';
$status = $_POST['status'] ?? 'Scheduled';

$errors = [];

// Validation
if (empty($truckId)) {
    $errors[] = 'Invalid truck ID.';
}

if (empty($plateNumber)) {
    $errors[] = 'Plate number is required.';
} else {
    // Check for duplicate plate number (excluding current truck)
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

if (empty($routeId)) {
    $errors[] = 'Route is required.';
}

// Validate schedule type
if (!in_array($scheduleType, ['daily', 'weekly'])) {
    $errors[] = 'Invalid schedule type.';
}

// Validate status
if (!in_array($status, ['Scheduled', 'Dispatched', 'Parked', 'Completed'])) {
    $errors[] = 'Invalid status.';
}

if (count($errors) === 0) {
    try {
        $truckModel = new Truck();
        $scheduleModel = new OperationSchedule();
        
        // Check if truck exists
        $truck = $truckModel->getTruckById($truckId);
        if (!$truck) {
            $errors[] = 'Truck not found.';
        } else {
            // Update truck
            $truckModel->update($truckId, $plateNumber, $bodyNumber, $foremanId);
            
            // Update schedule if it exists
            $schedule = $scheduleModel->getByTruckId($truckId);
            
            if ($schedule) {
                $scheduleModel->updateWithForeman(
                    $schedule['schedule_id'],
                    $routeId,
                    $foremanId,
                    $scheduleType,
                    $status
                );
            } else {
                // Create new schedule if it doesn't exist
                $routeModel = new Route();
                $route = $routeModel->getRouteById($routeId);
                
                if ($route) {
                    $adminId = $_SESSION['user_id'] ?? 1;
                    $operationId = 1; // Garbage Collection
                    
                    $scheduleModel->create(
                        $operationId,
                        $routeId,
                        $route['area_id'],
                        $truckId,
                        $adminId,
                        $foremanId,
                        $scheduleType,
                        $status
                    );
                }
            }
            
            $_SESSION['success'] = 'Truck updated successfully.';
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