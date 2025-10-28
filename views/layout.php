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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/public/assets/css/base.css">
    <link rel="stylesheet" href="/public/assets/css/layout.css">
    <link rel="stylesheet" href="/public/assets/css/admin.css">
    <link rel="stylesheet" href="/public/assets/css/foreman.css">

    <!-- DEBUG VERSION - Replace your script section with this -->
<script>
console.log('🔍 === COMPONENT LOADING DEBUG ===');
console.log('🔍 Testing each component for errors...');

// Test RouteMapSelector
try {
    console.log('🔍 RouteMapSelector class exists before load:', !!window.RouteMapSelector);
} catch (e) {
    console.error('❌ Error checking RouteMapSelector:', e);
}
</script>

<script src="/public/assets/js/components/RouteMapSelector.js"></script>
<script>
try {
    console.log('🔍 After RouteMapSelector load:');
    console.log('  - Class exists:', !!window.RouteMapSelector);
    if (window.RouteMapSelector) {
        console.log('  - Can instantiate: Testing...');
        // Don't actually instantiate, just check if constructor exists
        console.log('  - Constructor type:', typeof window.RouteMapSelector);
    }
} catch (e) {
    console.error('❌ RouteMapSelector error:', e);
}
</script>

<script src="/public/assets/js/components/Filters.js"></script>
<script>
try {
    console.log('🔍 After Filters load:');
    console.log('  - Class exists:', !!window.Filters);
} catch (e) {
    console.error('❌ Filters error:', e);
}
</script>

<script src="/public/assets/js/components/AddTruckView.js"></script>
<script>
try {
    console.log('🔍 After AddTruckView load:');
    console.log('  - Class exists:', !!window.AddTruckView);
} catch (e) {
    console.error('❌ AddTruckView error:', e);
}
</script>

<script src="/public/assets/js/components/EditTruckView.js"></script>
<script src="/public/assets/js/components/RouteDetailsView.js"></script>

<script>
console.log('🔍 === FINAL CHECK ===');
console.log('All components loaded:');
console.log('- RouteMapSelector:', !!window.RouteMapSelector);
console.log('- Filters:', !!window.Filters);
console.log('- AddTruckView:', !!window.AddTruckView);
console.log('- EditTruckView:', !!window.EditTruckView);
console.log('- RouteDetailsView:', !!window.RouteDetailsView);
</script>

<script src="/public/assets/js/boot-garbage-collection.js"></script>

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

    <?php if ($userType !== 'foreman'): ?>
            <div class="sidebar-overlay"></div>
        <?php endif; ?>

        <?php if ($userType === 'foreman'): ?>
            <?php require base_path('views/partials/foreman-bottom-nav.php'); ?>
        <?php endif; ?>
    </div>

    <!-- JavaScript Files -->
   <script src="/public/assets/js/layout.js"></script>
    
    <!-- Additional page-specific scripts -->
    <?= $additionalScripts ?? '' ?>

    <!-- Before </body> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>