<?php

use Models\User;

adminAuth();

$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Actually delete
    $id = $_POST['id'] ?? null;

    if ($id) {
        $userModel->delete($id, ['foreman']); // only delete foreman
    }

    header('Location: /admin/users');
    exit();
} else {
    // ✅ Show confirmation page
    $id = $_GET['id'] ?? null;

    if (!$id) {
        header('Location: /admin/users');
        exit();
    }

    $user = $userModel->findById($id, ['foreman']);

    if (!$user) {
        header('Location: /admin/users');
        exit();
    }

    view('admin/users/users.delete.view.php', [
        'user' => $user,
    ]);
}
