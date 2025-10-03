<?php

$router->get('/', function() {
    header('Location: /login');
    exit;
});

$router->get('/admin-styles.css', function() {
    header('Content-Type: text/css');
    readfile(__DIR__ . '/admin-styles.css');
    exit;
});

// Login Authentication
$router->get('/login', 'controllers/login.php');
$router->post('/login', 'controllers/login.php');
$router->get('/logout', 'controllers/logout.php');

// Admin Dashboard
$router->get('/admin', 'controllers/admin/dashboard.php');
//User Management
$router->get('/admin/users', 'controllers/admin/users/index.php');
$router->post('/admin/users', 'controllers/admin/users/store.php');
$router->get('/admin/users/edit', 'controllers/admin/users/edit.php');
$router->post('/admin/users/update', 'controllers/admin/users/update.php');
$router->post('/admin/users/delete', 'controllers/admin/users/delete.php');

// Reports
$router->get('/admin/reports', 'controllers/admin/reports/index.php');

//operations Management
$router->get('/admin/operations/collection', function() {
require base_path('views/admin/operations/collection.index.view.php');
});
$router->get('/admin/operations/sweeping', function() {
    require base_path('views/admin/operations/sweeping.index.view.php');
});
$router->get('/admin/operations/flushing', function() {
    require base_path('views/admin/operations/flushing.index.view.php');
});
$router->get('/admin/operations/de-clogging', function() {
    require base_path('views/admin/operations/de-clogging.index.view.php');
});
$router->get('/admin/operations/cleanup', function() {
    require base_path('views/admin/operations/cleanup.index.view.php');
});

// Foreman
$router->get('/foreman', 'controllers/foreman/dashboard.php');