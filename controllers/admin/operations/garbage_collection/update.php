<?php

adminAuth();

use Models\Truck;
use Models\OperationSchedule;

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
    $errors[] = 'Invalid truck.';
}

if (empty($plateNumber)) {
    $errors[] = 'Plate number is required.';
}

if (empty($bodyNumber)) {
    $errors[] = 'Body number is required.';
}

if (empty($foremanId)) {
    $errors[] = 'Foreman is required.';
}

if (count($errors) === 0) {
    try {
        $truckModel = new Truck();
        
        // Update truck
        $truckModel->update($truckId, $plateNumber, $bodyNumber, $foremanId);
        
        // Update schedule if route or schedule type changed
        if ($routeId) {
            $scheduleModel = new OperationSchedule();
            $schedule = $scheduleModel->getByTruckId($truckId);
            
            if ($schedule) {
                $scheduleModel->update($schedule['schedule_id'], $routeId, $scheduleType, $status);
            }
        }
        
        header('Location: /admin/operations/garbage_collection');
        exit();
    } catch (\Exception $e) {
        $errors[] = 'Error updating truck: ' . $e->getMessage();
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();