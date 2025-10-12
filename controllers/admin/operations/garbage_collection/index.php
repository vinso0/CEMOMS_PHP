<?php
adminAuth();

use Models\Schedule;

require_once base_path('models/Schedule.php');

$scheduleModel = new Schedule();
$collections = $scheduleModel->getAllSchedules();

require base_path('views/admin/operations/garbage_collection/index.view.php');