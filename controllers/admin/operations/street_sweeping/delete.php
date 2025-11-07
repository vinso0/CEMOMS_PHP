<?php
// controllers/admin/operations/street_sweeping/delete.php

adminAuth();

use Models\OperationSchedule;
use Models\Operation;
use Models\Route;

require_once base_path('models/OperationSchedule.php');
require_once base_path('models/Operation.php');
require_once base_path('models/Route.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/operations/street_sweeping');
    exit();
}

$scheduleId = $_POST['schedule_id'] ?? null;
$operationId = $_POST['operation_id'] ?? null;
$routeId = $_POST['route_id'] ?? null;

if (!$scheduleId || !$operationId || !$routeId) {
    $_SESSION['errors'] = ['Invalid request. Missing required IDs.'];
    header('Location: /admin/operations/street_sweeping');
    exit();
}

try {
    // Get database connection for transaction
    $db = \Core\App::resolve(\Core\Database::class);
    $pdo = $db->connection;
    
    // Start transaction
    $pdo->beginTransaction();
    
    $scheduleModel = new OperationSchedule();
    $operationModel = new Operation();
    $routeModel = new Route();
    
    // Step 1: Delete operation schedule (this will cascade to schedule_days if any)
    $scheduleModel->delete($scheduleId);
    
    // Step 2: Delete operation
    $operationModel->delete($operationId);
    
    // Step 3: Delete route (this will cascade to route_points)
    $routeModel->deleteRoute($routeId);
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['success'] = 'Street sweeping route deleted successfully.';
    
} catch (\Exception $e) {
    // Rollback transaction if it was started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    $_SESSION['errors'] = ['Error deleting street sweeping: ' . $e->getMessage()];
    error_log('Street sweeping deletion error: ' . $e->getMessage());
}

header('Location: /admin/operations/street_sweeping');
exit();
