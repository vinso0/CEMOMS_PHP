<?php

$router->get('/', function() {
    header('Location: /admin/login');
    exit;
});

$router->get('/admin-styles.css', function() {
    header('Content-Type: text/css');
    readfile(__DIR__ . '/admin-styles.css');
    exit;
});

// Admin Authentication
$router->get('/admin/login', 'controllers/admin/login.php');
$router->post('/admin/login', 'controllers/admin/login.php');
$router->get('/admin/logout', 'controllers/admin/logout.php');

// Admin Dashboard & User Management
$router->get('/admin', 'controllers/admin/dashboard.php');
$router->get('/admin/users', 'controllers/admin/users/index.php');
$router->post('/admin/users', 'controllers/admin/users/store.php');
$router->get('/admin/users/edit', 'controllers/admin/users/edit.php');
$router->post('/admin/users/update', 'controllers/admin/users/update.php');
$router->post('/admin/users/delete', 'controllers/admin/users/delete.php');
