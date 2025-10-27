<?php
// controllers/admin/operations/garbage_collection/store.php

adminAuth();

use Models\Truck;
use Models\OperationSchedule;
use Models\Route;
use Models\Operation;

require_once base_path('models/Truck.php');
require_once base_path('models/OperationSchedule.php');
require_once base_path('models/Route.php');
require_once base_path('models/Operation.php');

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

// Validate coordinates
if (empty($startLat) || empty($startLon)) {
    $errors[] = 'Start point coordinates are required. Please select a location on the map.';
}

if (empty($endLat) || empty($endLon)) {
    $errors[] = 'End point coordinates are required. Please select a location on the map.';
}

// Validate mid point coordinates if mid point address is provided
if (!empty($midPoint) && (empty($midLat) || empty($midLon))) {
    $errors[] = 'Mid point coordinates are required if mid point is specified. Please select a location on the map.';
}

if (!in_array($scheduleType, ['daily', 'weekly'])) {
    $errors[] = 'Invalid schedule type.';
}

// ENHANCED: Validate weekly schedule days
if ($scheduleType === 'weekly') {
    if (empty($_POST['schedule_days']) || !is_array($_POST['schedule_days'])) {
        $errors[] = 'Please select at least one day for weekly schedule.';
    } else {
        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($_POST['schedule_days'] as $day) {
            if (!in_array($day, $validDays)) {
                $errors[] = 'Invalid day selected: ' . htmlspecialchars($day);
            }
        }
    }
}

if (count($errors) === 0) {
    try {
        // Get database connection for transaction
        $db = \Core\App::resolve(\Core\Database::class);
        $pdo = $db->connection;
        
        // Start transaction
        $pdo->beginTransaction();
        
        $truckModel = new Truck();
        $scheduleModel = new OperationSchedule();
        $routeModel = new Route();
        $operationModel = new Operation();
        
        // Get admin ID
        $adminId = $_SESSION['user_id'] ?? 1;
        
        // Step 1: Create the operation first
        $operationName = "Garbage Collection - " . $plateNumber;
        $operationTypeId = 1; // Garbage Collection
        $operationId = $operationModel->create($operationName, $operationTypeId, $adminId, $bodyNumber);
        
        // Check if operation was created successfully
        if (!$operationId || $operationId == 0) {
            throw new \Exception('Failed to create operation record');
        }
        
        // Step 2: Create route with coordinates
        $routeId = $routeModel->createRouteWithCoordinates(
            $routeName,
            $startPoint, $startLat, $startLon,
            $endPoint, $endLat, $endLon,
            $midPoint, $midLat, $midLon
        );
        
        // Check if route was created successfully
        if (!$routeId || $routeId == 0) {
            throw new \Exception('Failed to create route record');
        }
        
        // Step 3: Create truck
        $truckId = $truckModel->create($plateNumber, $bodyNumber, $foremanId);
        
        // Check if truck was created successfully
        if (!$truckId || $truckId == 0) {
            throw new \Exception('Failed to create truck record');
        }
        
        // Step 4: Create operation schedule
        $areaId = 1;
        
        $scheduleId = $scheduleModel->create(
            $operationId,
            $routeId,
            $areaId,
            $truckId,
            $adminId,
            $foremanId,
            $scheduleType,
            'Parked' // Default status
        );
        
        // Check if schedule was created successfully
        if (!$scheduleId || $scheduleId == 0) {
            throw new \Exception('Failed to create schedule record');
        }
        
        // ENHANCED: Step 5 - Handle weekly schedule days
        if ($scheduleType === 'weekly' && !empty($_POST['schedule_days']) && is_array($_POST['schedule_days'])) {
            foreach ($_POST['schedule_days'] as $dayOfWeek) {
                $db->query(
                    "INSERT INTO schedule_days (schedule_id, day_of_week) VALUES (:schedule_id, :day_of_week)",
                    [
                        ':schedule_id' => $scheduleId,
                        ':day_of_week' => $dayOfWeek
                    ]
                );
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = 'Truck and route with coordinates added successfully.';
        header('Location: /admin/operations/garbage_collection');
        exit();
        
    } catch (\Exception $e) {
        // Rollback transaction if it was started
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        $errors[] = 'Error creating truck: ' . $e->getMessage();
        error_log('Truck creation error: ' . $e->getMessage()); // Log the actual error
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();
