<?php
// controllers/admin/operations/garbage_collection/delete.php

adminAuth();

use Models\Truck;
use Models\OperationSchedule;

require_once base_path('models/Truck.php');
require_once base_path('models/OperationSchedule.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/garbage_collection');
    exit();
}

$truckId = $_POST['id'] ?? null;
$errors = [];

// Validation
if (empty($truckId)) {
    $errors[] = 'Invalid truck ID.';
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
            // Check for active schedules
            if ($scheduleModel->hasActiveSchedules($truckId)) {
                $errors[] = 'Cannot delete truck with active schedules. Please complete or cancel schedules first.';
            } else {
                // Delete all completed schedules for this truck first
                $scheduleModel->deleteByTruckId($truckId);
                
                // Now delete the truck
                $truckModel->delete($truckId);
                
                $_SESSION['success'] = 'Truck deleted successfully.';
                header('Location: /admin/operations/garbage_collection');
                exit();
            }
        }
    } catch (\Exception $e) {
        $errors[] = 'Error deleting truck: ' . $e->getMessage();
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();