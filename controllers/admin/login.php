<?php

use Core\App;
use Core\Database;

session_start();

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header('Location: /admin');
    exit();
}

$db = App::resolve(Database::class);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = $_POST['username'] ?? ''; // can be username OR email
    $password   = $_POST['password'] ?? '';

    // Try to match either username OR email
    $user = $db->query('SELECT * FROM users WHERE (username = :login OR email = :login) AND role = "admin"', [
        ':login' => $loginInput
    ])->find();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];

        header('Location: /admin');
        exit();
    } else {
        $error = 'Invalid credentials.';
    }
}

view('admin/login.view.php', [
    'error' => $error,
]);
