<?php
$title = 'Admin Dashboard - CEMOMS';
$pageTitle = 'Dashboard';
$pageSubtitle = 'Overview of system activities';

ob_start();
?>
<div class="content-header">
    <h1 class="content-title">Dashboard</h1>
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

<!-- Recent Activity -->
<div class="users-table-container">
    <div class="table-header">
        <h2 class="section-title">Recent System Activity</h2>
    </div>
    <div style="padding: 25px;">
        <div class="activity-item">
            <div class="activity-icon" style="background: #4CAF50;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="activity-content">
                <h4>New user registered</h4>
                <p>John Doe joined as Personnel</p>
                <span class="activity-time">2 hours ago</span>
            </div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #2196F3;">
                <i class="fas fa-file"></i>
            </div>
            <div class="activity-content">
                <h4>Report generated</h4>
                <p>Monthly operations report completed</p>
                <span class="activity-time">5 hours ago</span>
            </div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #FF9800;">
                <i class="fas fa-edit"></i>
            </div>
            <div class="activity-content">
                <h4>User profile updated</h4>
                <p>Jane Smith updated her contact information</p>
                <span class="activity-time">1 day ago</span>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$additionalScripts = '
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .stat-info h3 {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .activity-content h4 {
        margin: 0 0 5px 0;
        color: #2c3e50;
    }

    .activity-content p {
        margin: 0 0 5px 0;
        color: #666;
        font-size: 0.9rem;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #999;
    }
</style>
';
require base_path('views/admin/layout.php');