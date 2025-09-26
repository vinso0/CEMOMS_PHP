<?php

use Core\App;
use Core\Database;

adminAuth();

$db = App::resolve(Database::class);

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

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

if (!in_array($role, ['personnel', 'foreman'])) {
    $errors[] = 'Invalid role selected.';
}

if (count($errors) === 0) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $db->query("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)", [
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':role' => $role,
    ]);

    header('Location: /admin/users');
    exit();
}

view('admin/users/create.view.php', [
    'errors' => $errors,
    'old' => $_POST,
]);
