<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-leaf"></i>
        </div>
        <span class="sidebar-title">CEMOMS</span>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="/admin" class="nav-link <?= urlIs('/admin') ? 'active' : '' ?>">
                <i class="fas fa-th-large nav-icon"></i>
                Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/reports" class="nav-link <?= urlIs('/admin/reports') ? 'active' : '' ?>">
                <i class="fas fa-chart-bar nav-icon"></i>
                Reports
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/operations" class="nav-link <?= urlIs('/admin/operations') ? 'active' : '' ?>">
                <i class="fas fa-cogs nav-icon"></i>
                Operations Management
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/users" class="nav-link <?= (urlIs('/admin/users') || urlIs('/admin/users/edit')) ? 'active' : '' ?>">
                <i class="fas fa-users nav-icon"></i>
                Users Management
            </a>
        </div>
        <div class="nav-item">
            <a href="/admin/settings" class="nav-link <?= urlIs('/admin/settings') ? 'active' : '' ?>">
                <i class="fas fa-cog nav-icon"></i>
                Settings
            </a>
        </div>
    </nav>
    
    <div class="logout-section">
        <a href="/admin/logout" class="logout-link">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            Logout
        </a>
    </div>
</aside>