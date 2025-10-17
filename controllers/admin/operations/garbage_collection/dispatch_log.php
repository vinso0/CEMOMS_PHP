<?php
// controllers/admin/operations/garbage_collection/dispatch_log.php

adminAuth();

use Models\OperationSchedule;

require_once base_path('models/OperationSchedule.php');

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

if (empty($date)) {
    $errors[] = 'Date is required.';
} else {
    // Validate date format
    $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        $errors[] = 'Invalid date format.';
    }
}

if (empty($dispatchTime)) {
    $errors[] = 'Dispatch time is required.';
} else {
    // Validate time format
    $timeObj = \DateTime::createFromFormat('H:i', $dispatchTime);
    if (!$timeObj) {
        $errors[] = 'Invalid dispatch time format.';
    }
}

// Validate return time if provided
if (!empty($returnTime)) {
    $timeObj = \DateTime::createFromFormat('H:i', $returnTime);
    if (!$timeObj) {
        $errors[] = 'Invalid return time format.';
    } else {
        // Check if return time is after dispatch time
        $dispatchDateTime = new \DateTime("$date $dispatchTime");
        $returnDateTime = new \DateTime("$date $returnTime");
        
        if ($returnDateTime <= $dispatchDateTime) {
            $errors[] = 'Return time must be after dispatch time.';
        }
    }
}

if (count($errors) === 0) {
    try {
        $scheduleModel = new OperationSchedule();
        
        // Get the schedule for this truck
        $schedule = $scheduleModel->getByTruckId($truckId);
        
        if (!$schedule) {
            $errors[] = 'No schedule found for this truck.';
        } else {
            $scheduleId = $schedule['schedule_id'];
            
            // Log dispatch - this sets status to "Dispatched"
            $dispatchDateTime = $date . ' ' . $dispatchTime . ':00';
            $scheduleModel->logDispatch($scheduleId, $dispatchDateTime);
            
            // Log return if provided - this sets status to "Parked"
            if (!empty($returnTime)) {
                $returnDateTime = $date . ' ' . $returnTime . ':00';
                $scheduleModel->logReturn($scheduleId, $returnDateTime);
                $_SESSION['success'] = 'Dispatch and return logged successfully. Truck status set to Parked.';
            } else {
                $_SESSION['success'] = 'Dispatch logged successfully. Truck status set to Dispatched.';
            }
            
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