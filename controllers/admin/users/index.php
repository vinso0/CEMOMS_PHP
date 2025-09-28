<?php

use Models\User;

adminAuth();

$userModel = new User();
$users = $userModel->findAll(['admin']);

view('admin/users/users.index.view.php', [
    'users' => $users,
]);
