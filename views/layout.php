<?php
// layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CEMOMS' ?></title>

    <!-- Bootstrap & Leaflet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/foreman.css">
    
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

        <?php if ($userType === 'foreman'): ?>
            <?php require base_path('views/partials/foreman-bottom-nav.php'); ?>
        <?php endif; ?>
    </div>

    <!-- Core JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/assets/js/layout.js"></script>

    <!-- Component Debug -->
    <script>
    console.log('🔍 === COMPONENT LOADING DEBUG ===');
    console.log('Testing component availability...');
    </script>

    <!-- Component Scripts -->
    <script src="/assets/js/components/RouteMapSelector.js"></script>
    <script src="/assets/js/components/Filters.js"></script>
    <script src="/assets/js/components/AddTruckView.js"></script>
    <script src="/assets/js/components/EditTruckView.js"></script>
    <script src="/assets/js/components/RouteDetailsView.js"></script>
    
    <!-- Component Initialization -->
    <script>
    console.log('🔍 === COMPONENT STATUS ===');
    ['RouteMapSelector', 'Filters', 'AddTruckView', 'EditTruckView', 'RouteDetailsView'].forEach(component => {
        console.log(`${component}: ${window[component] ? '✅' : '❌'}`);
    });
    </script>

    <!-- Application Boot -->
    <script src="/assets/js/boot-garbage-collection.js"></script>
    
    <!-- Additional page-specific scripts -->
    <?= $additionalScripts ?? '' ?>
</body>
</html>