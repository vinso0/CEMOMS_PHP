<?php

use Models\Foreman;
use Models\ForemanRole;

adminAuth();

$foremanModel = new Foreman();
$foremanRoleModel = new ForemanRole();

$foremen = $foremanModel->findAll();
$roles = $foremanRoleModel->getAllRoles();

view('admin/users/users.index.view.php', [
    'users' => $foremen,
    'roles' => $roles
]);