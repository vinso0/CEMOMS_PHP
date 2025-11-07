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

// Get truck ID - simpler validation
$truckId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

$errors = [];

// Validation
if ($truckId <= 0) {
    $errors[] = 'Invalid truck ID.';
}

if (empty($errors)) {
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
                $plateNumber = $truck['plate_number'] ?? 'Unknown';
                
                // Delete schedules first
                $scheduleModel->deleteByTruckId($truckId);
                
                // Delete truck
                $truckModel->delete($truckId);
                
                $_SESSION['success'] = "Truck '$plateNumber' deleted successfully.";
                header('Location: /admin/operations/garbage_collection');
                exit();
            }
        }
    } catch (\Exception $e) {
        $errors[] = 'Error deleting truck: ' . $e->getMessage();
    }
}

// Redirect with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();
