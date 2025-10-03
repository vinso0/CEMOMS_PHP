<?php
adminAuth();
view('foreman/dashboard.view.php', [
    'username' => $_SESSION['username'],
]);