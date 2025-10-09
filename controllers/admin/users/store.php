<?php

use Models\Foreman;
use Models\ForemanRole;

adminAuth();

$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$roleId   = $_POST['role'] ?? '';

$errors = [];

if (!$username) {
    $errors[] = 'Username is required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}

if (!$password) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}

if (!$roleId) {
    $errors[] = 'Role is required.';
}

if (count($errors) === 0) {
    $foremanModel = new Foreman();
    $foremanModel->create($username, $email, $password, $roleId);

    header('Location: /admin/users');
    exit();
}

// If there are errors, reload the page with roles
$foremanRoleModel = new ForemanRole();
$roles = $foremanRoleModel->getAllRoles();

view('admin/users/users.index.view.php', [
    'errors' => $errors,
    'old'    => $_POST,
    'users'  => [],
    'roles'  => $roles
]);