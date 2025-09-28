<?php
// layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'CEMOMS' ?></title>
    <link rel="stylesheet" href="assets/images/admin-styles.css">
</head>
<body>
    <?php require base_path('views/admin/partials/navbar.php'); ?>
    <?php require base_path('views/admin/partials/sidebar.php'); ?>

    <main class="main-content">
        <?= $content ?? '' ?>
    </main>

    <?= $additionalScripts ?? '' ?>
</body>
</html>