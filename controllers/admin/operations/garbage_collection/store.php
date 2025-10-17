<?php
// controllers/admin/operations/garbage_collection/store.php

adminAuth();

use Models\Truck;
use Models\OperationSchedule;
use Models\Route;

require_once base_path('models/Truck.php');
require_once base_path('models/OperationSchedule.php');
require_once base_path('models/Route.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/garbage_collection');
    exit();
}

$plateNumber = trim($_POST['plate_number'] ?? '');
$bodyNumber = trim($_POST['body_number'] ?? '');
$foremanId = $_POST['foreman_id'] ?? null;
$routeId = $_POST['route_id'] ?? null;
$scheduleType = $_POST['schedule_type'] ?? 'daily';
$status = $_POST['status'] ?? 'Scheduled';

$errors = [];

// Validation
if (empty($plateNumber)) {
    $errors[] = 'Plate number is required.';
} else {
    // Check for duplicate plate number
    $truckModel = new Truck();
    if ($truckModel->plateNumberExists($plateNumber)) {
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
        $routeModel = new Route();
        
        // Create truck
        $truckId = $truckModel->create($plateNumber, $bodyNumber, $foremanId);
        
        // Get route to determine area
        $route = $routeModel->getRouteById($routeId);
        if (!$route) {
            throw new \Exception('Selected route not found.');
        }
        
        $areaId = $route['area_id'];
        
        // Create operation schedule
        $adminId = $_SESSION['user_id'] ?? 1;
        $operationId = 1; // Garbage Collection operation type
        
        $scheduleModel->create(
            $operationId,
            $routeId,
            $areaId,
            $truckId,
            $adminId,
            $foremanId,
            $scheduleType,
            $status
        );
        
        $_SESSION['success'] = 'Truck added successfully.';
        header('Location: /admin/operations/garbage_collection');
        exit();
        
    } catch (\Exception $e) {
        $errors[] = 'Error creating truck: ' . $e->getMessage();
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();