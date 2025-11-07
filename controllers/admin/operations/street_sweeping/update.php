<?php
// controllers/admin/operations/street_sweeping/update.php

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

$scheduleId = $_POST['schedule_id'] ?? null;
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
if (empty($scheduleId) || !is_numeric($scheduleId)) {
    $errors[] = 'Invalid schedule ID.';
}

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

        // Get existing schedule to get operation and route IDs
        $existingSchedule = $scheduleModel->getById($scheduleId);
        if (!$existingSchedule) {
            throw new \Exception('Schedule not found');
        }

        $operationId = $existingSchedule['operation_id'];
        $routeId = $existingSchedule['route_id'];

        // Step 1: Update the operation name and description
        $operationName = "Street Sweeping - " . $routeName;
        $operationModel->update($operationId, $operationName, $routeName);

        // Step 2: Update route with coordinates
        $routeModel->updateRouteWithCoordinates(
            $routeId,
            $routeName,
            $startPoint, $startLat, $startLon,
            $endPoint, $endLat, $endLon,
            $midPoint, $midLat, $midLon
        );

        // Step 3: Update operation schedule with foreman
        $scheduleType = 'Daily'; // Street sweeping is always daily
        $currentStatus = $existingSchedule['status'] ?? 'Scheduled';

        $scheduleModel->updateWithForeman(
            $scheduleId,
            $routeId,
            $foremanId,
            $scheduleType,
            $currentStatus // Keep existing status
        );
        
        // Step 5: Update operation time separately if needed
        if ($operationTime) {
            $sql = "UPDATE operation_schedule 
                    SET operation_time = :operation_time
                    WHERE schedule_id = :schedule_id";
            
            $db->query($sql, [
                ':operation_time' => $operationTime,
                ':schedule_id' => $scheduleId
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = 'Street sweeping route updated successfully.';
        header('Location: /admin/operations/street_sweeping');
        exit();
        
    } catch (\Exception $e) {
        // Rollback transaction if it was started
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        $errors[] = 'Error updating street sweeping: ' . $e->getMessage();
        error_log('Street sweeping update error: ' . $e->getMessage());
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/street_sweeping');
exit();
