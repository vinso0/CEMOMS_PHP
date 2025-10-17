<?php

adminAuth();

use Models\OperationSchedule;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/garbage_collection');
    exit();
}

$truckId = $_POST['truck_id'] ?? null;
$date = $_POST['date'] ?? date('Y-m-d');
$dispatchTime = $_POST['dispatch_time'] ?? null;
$returnTime = $_POST['return_time'] ?? null;

$errors = [];

// Validation
if (empty($truckId)) {
    $errors[] = 'Truck is required.';
}

if (empty($dispatchTime)) {
    $errors[] = 'Dispatch time is required.';
}

if (count($errors) === 0) {
    try {
        $scheduleModel = new OperationSchedule();
        
        // Get the schedule for this truck
        $schedule = $scheduleModel->getByTruckId($truckId);
        
        if ($schedule) {
            $scheduleId = $schedule['schedule_id'];
            
            // Log dispatch
            $dispatchDateTime = $date . ' ' . $dispatchTime;
            $scheduleModel->logDispatch($scheduleId, $dispatchDateTime);
            
            // Log return if provided
            if (!empty($returnTime)) {
                $returnDateTime = $date . ' ' . $returnTime;
                $scheduleModel->logReturn($scheduleId, $returnDateTime);
            }
        } else {
            $errors[] = 'No schedule found for this truck.';
        }
        
        if (count($errors) === 0) {
            $_SESSION['success'] = 'Dispatch logged successfully.';
            header('Location: /admin/operations/garbage_collection');
            exit();
        }
    } catch (\Exception $e) {
        $errors[] = 'Error logging dispatch: ' . $e->getMessage();
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();