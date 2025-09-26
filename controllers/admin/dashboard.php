<?php
adminAuth();
view('admin/dashboard.view.php', [
    'username' => $_SESSION['username'],
]);