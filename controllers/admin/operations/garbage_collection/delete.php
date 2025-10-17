<?php
adminAuth();

use Models\OperationSchedule;

require_once base_path('models/Schedule.php');

$scheduleModel = new OperationSchedule();
$id = $_GET['id'];

$scheduleModel->delete($id);

header('Location: /admin/operations/garbage_collection');
exit;