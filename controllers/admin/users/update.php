<?php

use Models\Foreman;
use Models\ForemanRole;

adminAuth();

$id       = $_POST['id'] ?? null;
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$roleId   = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';

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

if ($password && strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}

if (count($errors) === 0) {
    $foremanModel = new Foreman();
    
    // Only pass password if it's not empty
    if ($password) {
        $foremanModel->update($id, $username, $email, $roleId, $password);
    } else {
        $foremanModel->update($id, $username, $email, $roleId);
    }

    header('Location: /admin/users');
    exit();
}

// If there are errors, show edit form again
$foremanRoleModel = new ForemanRole();
$roles = $foremanRoleModel->getAllRoles();

$user = [
    'id'              => $id,
    'username'        => $username,
    'email'           => $email,
    'foreman_role_id' => $roleId,
];

view('admin/users/users.edit.view.php', [
    'errors' => $errors,
    'user'   => $user,
    'roles'  => $roles
]);