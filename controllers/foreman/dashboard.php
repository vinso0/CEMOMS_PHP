<?php

foremanAuth();

view('foreman/dashboard.view.php', [
    'username' => $_SESSION['username'] ?? 'Foreman',
    'role' => $_SESSION['role'] ?? 'Foreman'
]);