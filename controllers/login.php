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

        // ✅ maybe redirect to dashboard or admin
        header('Location: /admin');
        exit();
    } else {
        $error = 'Invalid credentials.';
    }
}

// If already logged in, prevent showing login again
if (isset($_SESSION['user_id'])) {
    header('Location: /admin'); 
    exit();
}

view('/login.view.php', [
    'error' => $error,
]);
