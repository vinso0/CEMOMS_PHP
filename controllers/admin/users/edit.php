<?php

use Core\App;
use Core\Database;

adminAuth();

$db = App::resolve(Database::class);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: /admin/users');
    exit();
}

$user = $db->query("SELECT id, username, email, role FROM users WHERE id = :id AND role IN ('personnel', 'foreman')", [
    ':id' => $id
])->find();

if (!$user) {
    header('Location: /admin/users');
    exit();
}

view('admin/users/edit.view.php', [
    'user' => $user,
]);
