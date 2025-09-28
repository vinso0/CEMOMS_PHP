<?php

use Models\User;

adminAuth();

$id       = $_POST['id'] ?? null;
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$roleId   = $_POST['role'] ?? ''; // now stores role_id

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

if (!$roleId) {
    $errors[] = 'Role is required.';
}

if (count($errors) === 0) {
    $userModel = new User();
    $userModel->update($id, $username, $email, $roleId);

    header('Location: /admin/users');
    exit();
}

$user = [
    'id'       => $id,
    'username' => $username,
    'email'    => $email,
    'role_id'  => $roleId,
];

view('admin/users/users.edit.view.php', [
    'errors' => $errors,
    'user'   => $user,
]);
