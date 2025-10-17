<?php

foremanAuth();

// In a real app you'd pull notifications from DB; provide simple placeholder data for now
$notifications = [
    ['id'=>1, 'title'=>'Report Approved', 'body'=>'Your report #123 was approved by the admin.', 'time'=>'2025-10-08 14:33'],
    ['id'=>2, 'title'=>'Reminder', 'body'=>'Please submit your daily activity report.', 'time'=>'2025-10-09 08:12'],
];

view('foreman/notifications/notifications.index.view.php', [
    'notifications' => $notifications,
]);
