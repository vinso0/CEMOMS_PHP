<?php

$router->get('/', function() {
    header('Location: /login');
    exit;
});

// Login Authentication
$router->get('/login', 'controllers/login.php');
$router->post('/login', 'controllers/login.php');
$router->get('/logout', 'controllers/logout.php');

// Admin Dashboard
$router->get('/admin', 'controllers/admin/dashboard.php');

// User Management
$router->get('/admin/users', 'controllers/admin/users/index.php');
$router->post('/admin/users', 'controllers/admin/users/store.php');
$router->get('/admin/users/edit', 'controllers/admin/users/edit.php');
$router->post('/admin/users/update', 'controllers/admin/users/update.php');
$router->post('/admin/users/delete', 'controllers/admin/users/delete.php');

// Reports
$router->get('/admin/reports', 'controllers/admin/reports/index.php');

// Operations Management - Garbage Collection
$router->get('/admin/operations/garbage_collection', 'controllers/admin/operations/garbage_collection/index.php');
$router->post('/admin/operations/garbage_collection/truck/store', 'controllers/admin/operations/garbage_collection/store.php');
$router->post('/admin/operations/garbage_collection/truck/update', 'controllers/admin/operations/garbage_collection/update.php');
$router->post('/admin/operations/garbage_collection/log', 'controllers/admin/operations/garbage_collection/dispatch_log.php');

// Street Sweeping
$router->get('/admin/operations/sweeping', function() {
    require base_path('views/admin/operations/street_sweeping/sweeping.index.view.php');
});

// Flushing
$router->get('/admin/operations/flushing', function() {
    require base_path('views/admin/operations/flushing/flushing.index.view.php');
});

// De-Clogging
$router->get('/admin/operations/de-clogging', function() {
    require base_path('views/admin/operations/de-clogging/de-clogging.index.view.php');
});

// Cleanup Drives
$router->get('/admin/operations/cleanup', function() {
    require base_path('views/admin/operations/cleanup_drives/cleanup.index.view.php');
});

// Foreman
$router->get('/foreman', 'controllers/foreman/dashboard.php');