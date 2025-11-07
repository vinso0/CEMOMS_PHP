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
$router->post('/admin/operations/garbage_collection/store', 'controllers/admin/operations/garbage_collection/store.php');
$router->post('/admin/operations/garbage_collection/update', 'controllers/admin/operations/garbage_collection/update.php');
$router->post('/admin/operations/garbage_collection/delete', 'controllers/admin/operations/garbage_collection/delete.php');
//$router->get('/admin/operations/garbage_collection/get_route_points', 'controllers/admin/operations/garbage_collection/get_route_points.php');


// Street Sweeping Operations
// Redirect shorter URL to the full URL
$router->get('/admin/operations/sweeping', function() {
    header('Location: /admin/operations/street_sweeping');
    exit();
});

$router->get('/admin/operations/street_sweeping', 'controllers/admin/operations/street_sweeping/index.php');
$router->get('/admin/operations/street_sweeping/create', 'controllers/admin/operations/street_sweeping/create.php');
$router->post('/admin/operations/street_sweeping/store', 'controllers/admin/operations/street_sweeping/store.php');
$router->get('/admin/operations/street_sweeping/edit', 'controllers/admin/operations/street_sweeping/edit.php');
$router->post('/admin/operations/street_sweeping/update', 'controllers/admin/operations/street_sweeping/update.php');
$router->post('/admin/operations/street_sweeping/delete', 'controllers/admin/operations/street_sweeping/delete.php');
$router->get('/admin/operations/street_sweeping/get_route_points', 'controllers/admin/operations/street_sweeping/get_route_points.php');


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
$router->get('/foreman/reports', 'controllers/foreman/reports/index.php');
$router->get('/foreman/notifications', 'controllers/foreman/notifications/index.php');
$router->get('/foreman/account', 'controllers/foreman/account.php');

// API Routes
$router->get('/api/geocode', 'controllers/api/geocode.php');