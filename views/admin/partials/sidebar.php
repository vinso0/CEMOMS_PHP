<!-- Sidebar -->
<link rel="stylesheet" href="/admin-styles.css">
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="/assets/images/CEMD-Logo.png" alt="CEMOMS Logo" class="logo-image">
        </div>
        <span class="sidebar-title">CEMOMS</span>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="/admin" class="nav-link <?= urlIs('/admin') ? 'active' : '' ?>">
                <img src="/assets/images/dashboard.png" alt="Dashboard Icon" class="nav-icon">
                Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/reports" class="nav-link <?= urlIs('/admin/reports') ? 'active' : '' ?>">
                <img src="/assets/images/reports.png" alt="Reports Icon" class="nav-icon">
                Reports
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/operations" class="nav-link <?= urlIs('/admin/operations') ? 'active' : '' ?>">
                <img src="/assets/images/operations.png" alt="Operations Icon" class="nav-icon">
                Operations Management
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/users" class="nav-link <?= (urlIs('/admin/users') || urlIs('/admin/users/edit')) ? 'active' : '' ?>">
                <img src="/assets/images/users.png" alt="Users Icon" class="nav-icon">
                Users Management
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/settings" class="nav-link <?= urlIs('/admin/settings') ? 'active' : '' ?>">
                <img src="/assets/images/setting.png" alt="Settings Icon" class="nav-icon">
                Settings
            </a>
        </div>
    </nav>
    
    <div class="logout-section">
        
        <a href="/logout" class="logout-link">
            <img src="/assets/images/logout.png" alt="Logout Icon" class="logout-icon">
            Logout
        </a>
    </div>
</aside>