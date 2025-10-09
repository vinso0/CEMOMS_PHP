<?php

use Models\Foreman;
use Models\ForemanRole;

adminAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: /admin/users');
    exit();
}

$foremanModel = new Foreman();
$foremanRoleModel = new ForemanRole();

$user = $foremanModel->findById($id);
$roles = $foremanRoleModel->getAllRoles();

if (!$user) {
    header('Location: /admin/users');
    exit();
}

view('admin/users/users.edit.view.php', [
    'user' => $user,
    'roles' => $roles
]);