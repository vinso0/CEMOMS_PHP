<?php

use Models\User;

adminAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: /admin/users');
    exit();
}

$userModel = new User();
$user = $userModel->findById($id, ['personnel', 'foreman']);

if (!$user) {
    header('Location: /admin/users');
    exit();
}

view('admin/users/users.edit.view.php', [
    'user' => $user,
]);
