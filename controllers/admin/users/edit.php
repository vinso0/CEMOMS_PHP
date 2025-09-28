<?php

use models\User;
use models\Roles;

adminAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: /admin/users');
    exit();
}

$userModel = new User();
$roleModel = new Roles();
$user = $userModel->findById($id, ['foreman']);
$roles = $roleModel->getRoles(['admin']);

if (!$user) {
    header('Location: /admin/users');
    exit();
}

view('admin/users/users.edit.view.php', [
    'user' => $user,
    'roles' => $roles
]);
