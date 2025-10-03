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

// Admin
$router->get('/admin', 'controllers/admin/dashboard.php');
$router->get('/admin/reports', 'controllers/admin/reports/index.php');
$router->get('/admin/users', 'controllers/admin/users/index.php');
$router->post('/admin/users', 'controllers/admin/users/store.php');
$router->get('/admin/users/edit', 'controllers/admin/users/edit.php');
$router->post('/admin/users/update', 'controllers/admin/users/update.php');
$router->post('/admin/users/delete', 'controllers/admin/users/delete.php');

// Foreman
$router->get('/foreman', 'controllers/foreman/dashboard.php');