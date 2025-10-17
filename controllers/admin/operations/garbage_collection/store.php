<?php

adminAuth();

use Models\Truck;
use Models\OperationSchedule;
use Models\Route;

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

if (count($errors) === 0) {
    try {
        $truckModel = new Truck();
        $scheduleModel = new OperationSchedule();
        
        // Create truck
        $truckId = $truckModel->create($plateNumber, $bodyNumber, $foremanId);
        
        // Get route to determine area
        $routeModel = new Route();
        $route = $routeModel->getRouteById($routeId);
        $areaId = $route['area_id'] ?? 1;
        
        // Create operation schedule
        $adminId = $_SESSION['user_id'] ?? 1;
        $operationId = 1; // You may need to create an operation first or handle this differently
        
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
        
        header('Location: /admin/operations/garbage_collection');
        exit();
    } catch (\Exception $e) {
        $errors[] = 'Error creating truck: ' . $e->getMessage();
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/collection');
exit();