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

// Get truck ID from POST - more robust handling
$truckId = $_POST['id'] ?? $_POST['truck_id'] ?? null;

// Convert to integer and validate
$truckId = filter_var($truckId, FILTER_VALIDATE_INT);

$errors = [];

// Validation
if (!$truckId || $truckId <= 0) {
    $errors[] = 'Invalid truck ID.';
    error_log('Delete truck failed: Invalid ID received - ' . print_r($_POST, true));
}

if (count($errors) === 0) {
    try {
        $truckModel = new Truck();
        $scheduleModel = new OperationSchedule();
        
        // Check if truck exists
        $truck = $truckModel->getTruckById($truckId);
        
        if (!$truck) {
            $errors[] = 'Truck not found.';
            error_log("Delete truck failed: Truck ID $truckId not found in database");
        } else {
            // Check for active schedules
            if ($scheduleModel->hasActiveSchedules($truckId)) {
                $errors[] = 'Cannot delete truck with active schedules. Please complete or cancel schedules first.';
            } else {
                // Store plate number for success message
                $plateNumber = $truck['plate_number'] ?? 'Unknown';
                
                // Delete all completed schedules for this truck first
                $scheduleModel->deleteByTruckId($truckId);
                
                // Now delete the truck
                $truckModel->delete($truckId);
                
                $_SESSION['success'] = "Truck '$plateNumber' deleted successfully.";
                error_log("Truck deleted successfully: ID $truckId, Plate: $plateNumber");
                header('Location: /admin/operations/garbage_collection');
                exit();
            }
        }
    } catch (\Exception $e) {
        $errors[] = 'Error deleting truck: ' . $e->getMessage();
        error_log('Delete truck exception: ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
    }
}

// If there are errors, redirect back with errors
$_SESSION['errors'] = $errors;
header('Location: /admin/operations/garbage_collection');
exit();
