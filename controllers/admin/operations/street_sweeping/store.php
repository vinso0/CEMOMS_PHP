<?php
// controllers/admin/operations/street_sweeping/store.php

adminAuth();

use Models\OperationSchedule;
use Models\Route;
use Models\Operation;

require_once base_path('models/OperationSchedule.php');
require_once base_path('models/Route.php');
require_once base_path('models/Operation.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/street_sweeping');
    exit();
}

$foremanId = $_POST['foreman_id'] ?? null;
$operationTime = $_POST['operation_time'] ?? null;

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
if (empty($foremanId) || !is_numeric($foremanId)) {
    $errors[] = 'Valid street sweeping foreman selection is required.';
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

if (empty($operationTime)) {
    $errors[] = 'Operation time is required.';
}

if (count($errors) === 0) {
    try {
        // Get database connection for transaction
        $db = \Core\App::resolve(\Core\Database::class);
        $pdo = $db->connection;
        
        // Start transaction
        $pdo->beginTransaction();
        
        $scheduleModel = new OperationSchedule();
        $routeModel = new Route();
        $operationModel = new Operation();
        
        // Get admin ID
        $adminId = $_SESSION['user_id'] ?? 1;
        
        // Step 1: Create the operation first
        $operationName = "Street Sweeping - " . $routeName;
        $operationTypeId = 2; // Street Sweeping
        $operationId = $operationModel->create($operationName, $operationTypeId, $adminId, $routeName);
        
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
        
        // Step 3: Create operation schedule (no truck for street sweeping)
        $areaId = 1;
        $scheduleType = 'Daily'; // Street sweeping is always daily
        
        $scheduleId = $scheduleModel->create(
            $operationId,
            $routeId,
            $areaId,
            null, // No truck_id for street sweeping
            $adminId,
            $foremanId,
            $scheduleType,
            'Scheduled', // Default status
            $operationTime
        );
        
        // Check if schedule was created successfully
        if (!$scheduleId || $scheduleId == 0) {
            throw new \Exception('Failed to create schedule record');
        }
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = 'Street sweeping route added successfully.';
        header('Location: /admin/operations/street_sweeping');
        exit();
        
    } catch (\Exception $e) {
        // Rollback transaction if it was started
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        $errors[] = 'Error creating street sweeping: ' . $e->getMessage();
        error_log('Street sweeping creation error: ' . $e->getMessage());
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/street_sweeping');
exit();
