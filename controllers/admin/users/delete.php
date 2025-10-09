<?php

use Models\Foreman;

adminAuth();

$foremanModel = new Foreman();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the foreman
    $id = $_POST['id'] ?? null;

    if ($id) {
        $foremanModel->delete($id);
    }

    header('Location: /admin/users');
    exit();
} else {
    // Show confirmation page
    $id = $_GET['id'] ?? null;

    if (!$id) {
        header('Location: /admin/users');
        exit();
    }

    $user = $foremanModel->findById($id);

    if (!$user) {
        header('Location: /admin/users');
        exit();
    }

    view('admin/users/users.delete.view.php', [
        'user' => $user,
    ]);
}