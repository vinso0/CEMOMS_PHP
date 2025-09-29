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
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>Total Users</h3>
            <span class="stat-number"><?= $stats['total_users'] ?? 0 ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-route"></i>
        </div>
        <div class="stat-info">
            <h3>Active Routes</h3>
            <span class="stat-number"><?= $stats['active_routes'] ?? 0 ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h3>Pending Tasks</h3>
            <span class="stat-number"><?= $stats['pending_tasks'] ?? 0 ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #9C27B0;">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-info">
            <h3>Reports</h3>
            <span class="stat-number"><?= $stats['total_reports'] ?? 0 ?></span>
        </div>
    </div>
</div>

<div class="content-header">
    <h2 class="content-subtitle">Operation Activities</h2>
</div>

<!-- Quick Actions Section -->
<div class="content-header" style="margin-top: 40px;">
    <h2 class="content-title" style="font-size: 1.5rem;">Quick Actions</h2>
</div>

<div class="stats-grid">
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='/admin/users'">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-info">
            <h3>Manage Users</h3>
            <span style="font-size: 1rem; color: #666;">Add, edit, or remove users</span>
        </div>
    </div>
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='/admin/reports'">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-info">
            <h3>View Reports</h3>
            <span style="font-size: 1rem; color: #666;">Generate and view reports</span>
        </div>
    </div>
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='/admin/operations'">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h3>Operations</h3>
            <span style="font-size: 1rem; color: #666;">Manage daily operations</span>
        </div>
    </div>
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='/admin/settings'">
        <div class="stat-icon" style="background: #9C27B0;">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="stat-info">
            <h3>Settings</h3>
            <span style="font-size: 1rem; color: #666;">System configuration</span>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

require base_path('views/layout.php');