<?php

use Core\App;
use Core\Database;

adminAuth();

$db = App::resolve(Database::class);

$id = $_POST['id'] ?? null;
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$role = $_POST['role'] ?? '';

$errors = [];

if (!$id) {
    $errors[] = 'Invalid user.';
}

if (!$username) {
    $errors[] = 'Username is required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}

if (!in_array($role, ['personnel', 'foreman'])) {
    $errors[] = 'Invalid role selected.';
}

if (count($errors) === 0) {
    $db->query("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id AND role IN ('personnel', 'foreman')", [
        ':username' => $username,
        ':email' => $email,
        ':role' => $role,
        ':id' => $id,
    ]);

    header('Location: /admin/users');
    exit();
}

$user = [
    'id' => $id,
    'username' => $username,
    'email' => $email,
    'role' => $role,
];

view('admin/users/edit.view.php', [
    'errors' => $errors,
    'user' => $user,
]);
