<?php
adminAuth();

use Models\Schedule;

require_once base_path('models/Schedule.php');

$scheduleModel = new Schedule();

$scheduleModel->addSchedule(
    null, // operation_id, if not used, pass null or remove from method
    $_POST['truck_id'],
    $_POST['route_id'],
    null, // foreman_id, if not used, pass null or remove from method
    'garbage_collection', // or whatever schedule_type you use
    $_POST['collection_date'],
    $_POST['status']
);

header('Location: /admin/operations/garbage_collection');
exit;