<?php
// layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CEMOMS' ?></title>

    <!-- In <head> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Additional page-specific styles -->
    <?= $additionalStyles ?? '' ?>
</head>
<body>
    <div class="app-layout">
        <?php require base_path('views/partials/sidebar.php'); ?>
        
        <div class="main-content">
            <?php require base_path('views/partials/navbar.php'); ?>
            
            <div class="page-content">
                <?= $content ?? '' ?>
            </div>
        </div>
        
        <!-- Sidebar overlay for mobile -->
        <div class="sidebar-overlay"></div>
    </div>

    <!-- JavaScript Files -->
    <script src="/assets/js/layout.js"></script>
    
    <!-- Additional page-specific scripts -->
    <?= $additionalScripts ?? '' ?>

    <!-- Before </body> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>