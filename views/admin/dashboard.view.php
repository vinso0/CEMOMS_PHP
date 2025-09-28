<?php
$title = 'Admin Dashboard - CEMOMS';
$pageTitle = 'Dashboard';

ob_start();
?>
<div class="content-header">
    <h1 class="content-title">Summary</h1>
    <h2 class="content-subtitle">Garbage Collection</h2>
</div>

<!-- Quick Stats Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3></h3>
            <span class="stat-number">0</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <h3></h3>
            <span class="stat-number">0</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h3></h3>
            <span class="stat-number">0</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #9C27B0;">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-info">
            <h3></h3>
            <span class="stat-number">0</span>
        </div>
    </div>
</div>

<div class="content-header">
    <h2 class="content-subtitle">Operation Activities</h2>
</div>

<?php
$content = ob_get_clean();

require base_path('views/layout.php');