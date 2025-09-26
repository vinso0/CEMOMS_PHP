<?php

use Core\App;
use Core\Database;

adminAuth();

$db = App::resolve(Database::class);

$users = $db->query("SELECT id, username, email, role FROM users WHERE role IN ('personnel', 'foreman')")->get();

view('admin/users/index.view.php', [
    'users' => $users,
]);
