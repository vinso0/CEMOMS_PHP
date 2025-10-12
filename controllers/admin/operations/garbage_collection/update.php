<?php
adminAuth();

use Models\Schedule;

require_once base_path('models/Schedule.php');

$scheduleModel = new Schedule();

$id = $_GET['id'];

$scheduleModel->updateSchedule(
    $id,
    $_POST['truck_id'],
    $_POST['route_id'],
    $_POST['collection_date'],
    $_POST['status']
);

header('Location: /admin/operations/garbage_collection');
exit;