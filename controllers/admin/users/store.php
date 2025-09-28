<?php

use Models\User;

adminAuth();

$userModel = new User();

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role_id = $_POST['role'] ?? '';

$errors = [];

if (!$username) $errors[] = 'Username is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (!$password) $errors[] = 'Password is required.';
if (!in_array($role_id, [2, 3])) $errors[] = 'Invalid role selected.';

if (empty($errors)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $userModel->create([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':role_id' => $role_id
    ]);

    header('Location: /admin/users');
    exit();
}

view('admin/users/create.view.php', [
    'errors' => $errors,
    'old' => $_POST,
]);
