<?php

use models\User;
use models\Roles;

adminAuth();

$userModel = new User();
$roleModel = new Roles();
$users = $userModel->findAll(['admin']);
$roles = $roleModel->getRoles(['admin']);

view('admin/users/users.index.view.php', [
    'users' => $users,
    'roles' => $roles
]);
