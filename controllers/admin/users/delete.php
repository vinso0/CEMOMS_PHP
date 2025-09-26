<?php

use Core\App;
use Core\Database;

adminAuth();

$db = App::resolve(Database::class);

$id = $_POST['id'] ?? null;

if ($id) {
    $db->query("DELETE FROM users WHERE id = :id AND role IN ('personnel', 'foreman')", [
        ':id' => $id,
    ]);
}

header('Location: /admin/users');
exit();
