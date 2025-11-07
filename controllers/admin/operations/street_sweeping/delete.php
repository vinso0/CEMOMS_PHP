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

$scheduleId = $_POST['id'] ?? null;

// Enhanced validation
if (empty($scheduleId) || !is_numeric($scheduleId)) {
    $_SESSION['errors'] = ['Invalid schedule ID.'];
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

    // Get schedule details to get operation and route IDs
    $schedule = $scheduleModel->getById($scheduleId);
    if (!$schedule) {
        throw new \Exception('Schedule not found');
    }

    $operationId = $schedule['operation_id'];
    $routeId = $schedule['route_id'];

    // Step 1: Delete operation schedule (this may cascade to schedule_days if configured)
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
