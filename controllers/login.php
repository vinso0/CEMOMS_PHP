<?php

use models\User;

session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = $_POST['username'] ?? ''; // username OR email
    $password   = $_POST['password'] ?? '';

    $userModel = new User();
    $user = $userModel->findByLogin($loginInput);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];

        // redirect based on role
        if ($user['role'] === 'admin') {
            header('Location: /admin');
        } else {
            header('Location: /foreman');
        }
        exit();
    } else {
        $error = 'Invalid credentials.';
    }
}

// If already logged in, prevent showing login again
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: /admin');
    } else {
        header('Location: /foreman');
    }
    exit();
}

view('/login.view.php', [
    'error' => $error,
]);