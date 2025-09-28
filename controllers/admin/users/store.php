<?php

use models\User;

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
}

if (!$roleId) {
    $errors[] = 'Role is required.';
}

if (count($errors) === 0) {
    $userModel = new User();
    $userModel->create($username, $email, $password, $roleId);

    header('Location: /admin/users');
    exit();
}

view('admin/users/create.view.php', [
    'errors' => $errors,
    'old'    => $_POST,
]);
