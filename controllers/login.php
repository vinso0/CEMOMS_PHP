<?php

use Models\Admin;
use Models\Foreman;

session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = $_POST['username'] ?? ''; // email or username
    $password   = $_POST['password'] ?? '';

    // Try to find user in admin table first
    $adminModel = new Admin();
    $admin = $adminModel->findByLogin($loginInput);

    if ($admin && password_verify($password, $admin['password'])) {
        // Admin login successful
        $_SESSION['user_id']   = $admin['id'];
        $_SESSION['email']     = $admin['email'];
        $_SESSION['role']      = 'admin';
        $_SESSION['user_type'] = 'admin';

        header('Location: /admin');
        exit();
    }

    // If not admin, try foreman table
    $foremanModel = new Foreman();
    $foreman = $foremanModel->findByLogin($loginInput);

    if ($foreman && password_verify($password, $foreman['password'])) {
        // Foreman login successful
        $_SESSION['user_id']        = $foreman['id'];
        $_SESSION['username']       = $foreman['username'];
        $_SESSION['email']          = $foreman['email'];
        $_SESSION['role']           = $foreman['role']; // role_name from foreman_role table
        $_SESSION['foreman_role_id'] = $foreman['foreman_role_id'];
        $_SESSION['user_type']      = 'foreman';

        header('Location: /foreman');
        exit();
    }

    // If neither admin nor foreman found
    $error = 'Invalid credentials.';
}

// If already logged in, redirect based on user type
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
        header('Location: /admin');
    } else {
        header('Location: /foreman');
    }
    exit();
}

view('/login.view.php', [
    'error' => $error,
]);