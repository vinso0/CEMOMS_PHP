<?php
adminAuth();

use Models\Schedule;

require_once base_path('models/Schedule.php');

$scheduleModel = new Schedule();
$id = $_GET['id'];

$scheduleModel->deleteSchedule($id);

header('Location: /admin/operations/garbage_collection');
exit;