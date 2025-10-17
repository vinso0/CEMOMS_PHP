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
$scheduleType = $_POST['schedule_type'] ?? 'daily';

// Route data
$routeName = trim($_POST['route_name'] ?? '');
$startPoint = trim($_POST['start_point'] ?? '');
$midPoint = trim($_POST['mid_point'] ?? ''); // Optional
$endPoint = trim($_POST['end_point'] ?? '');

$errors = [];

// Validation
if (empty($plateNumber)) {
    $errors[] = 'Plate number is required.';
} else {
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
        
        // Create route first
        $routeId = $routeModel->createRoute($routeName, $startPoint, $midPoint, $endPoint);
        
        // Create truck
        $truckId = $truckModel->create($plateNumber, $bodyNumber, $foremanId);
        
        // Create operation schedule with default status "Parked"
        $adminId = $_SESSION['user_id'] ?? 1;
        $operationId = 1; // Garbage Collection operation type
        $areaId = 1; // Default area (you can modify this later if needed)
        
        $scheduleModel->create(
            $operationId,
            $routeId,
            $areaId,
            $truckId,
            $adminId,
            $foremanId,
            $scheduleType,
            'Parked' // Default status
        );
        
        $_SESSION['success'] = 'Truck and route added successfully.';
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